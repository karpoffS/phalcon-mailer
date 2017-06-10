<?php

namespace Mailer\Parser;

use Closure;

use Jeremeamia\SuperClosure\SerializableClosure;
use Phalcon\Mvc\User\Component;

use Mailer\Models\EmailList;
use Mailer\Models\EmailImports;
use Mailer\Models\EmailGroups;
use Phalcon\Queue\Beanstalk;


use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\Query\Status;
use Phalcon\Mvc\Model\Message;


// Подключаем проверку адресов
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Warning\Warning;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;

// Изолированные транзакции
use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class ParserService  extends Component {


    /**
     * @var array
     */
    protected $config;

    /**
     * The Benastalk queue instance
     *
     * @var \Phalcon\Queue\Beanstalk
     */
    protected $queue;


    /**
     * Create a new service provider instance.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!$config && !$this->di->has('config')) {
            throw new \RuntimeException('Correct config for Mailer is not provided!');
        }

        $this->config = $config ?: $this->getDI()->get("config")->toArray();

        if ($this->getDI()->has('queue')) {
            $this->setQueue($this->getDI()->get('queue'));
        }
    }


    /**
     * Set the Beanstalk queue instance
     *
     * @param \Phalcon\Queue\Beanstalk $queue
     *
     * @return self
     */
    public function setQueue(Beanstalk $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Получить путь с пользовательской папкой
     * @return string
     */
    public function getPath($groupId = null, $userId) {
        if(!empty($groupId)){
            return $this->config['application']['uploadsDir']
            .$userId.DIRECTORY_SEPARATOR.$groupId;
        }
        return $this->config['application']['uploadsDir'].$userId;
    }

    /**
     * Проверяет MX запись у домена
     *
     * @param $host
     * @return bool
     */
    protected function checkDNS($host)
    {
        $Aresult = true;
        $MXresult = checkdnsrr($host, 'MX');

        if (!$MXresult) {
            $Aresult = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA');
        }
        return $MXresult || $Aresult;
    }


    /**
     * Парсинг файлов
     *
     * @param EmailImports $import
     * @return bool
     */
    public function parseFile(EmailImports $import){


        $result = false;

        // Проверка на MX запись
        $checkMXandRFC = $import->getChecking() > 0 ? true : false;

        // Путь к файлу
        $csv = $this->getPath($import->getGroupId(),$import->getUserId())
            . DIRECTORY_SEPARATOR . $import->getFilename();

        // Проверяем существование файла
        if (file_exists($csv)){

            // Открываем файл
            if (($handle = fopen($csv, "r")) !== FALSE) {

                $emailsList = [];

                if($checkMXandRFC){
                    $validDomains = [];
                    $invalidDomains = [];
                }

                // Читаем файл
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                    // Если пуста первая ячейка то пропускаем
                    if(empty($data[0])){
                        continue;
                    }

                    // Делаем проверку адресов на соответствие RFC и существование MX записи домена
                    if($checkMXandRFC){

                        // Ищем домен
                        $domain = mb_substr($data[0], mb_strpos($data[0], '@')+1);

                        // Объект проверки Email адресов
                        $validator = new EmailValidator();

                        // Множественная проверка
                        $multipleValidations = new MultipleValidationWithAnd([
                            new RFCValidation(),
                            new DNSCheckValidation()
                        ]);

                        // Запускаем проверку на валидность email адресов
                        if(!in_array($domain, $validDomains)){
                            if(!$validator->isValid($data[0], $multipleValidations)){

                                /** @var Warning $warning */
                                foreach ($validator->getWarnings() as $warning) {
                                    echo "Warning : {$warning->message()} address {$data[0]}".PHP_EOL;
                                }

                                array_push($invalidDomains, $domain);

                                continue;
                            }
                        }

                        // Всё гуд работаем дальше
                        if(!in_array($domain, $validDomains)){
                            array_push($validDomains, $domain);
                        }
                    }

                    // Собираем массив
                    $emailsList[] = [
                        'userId' => $import->getUserId(),
                        'groupId' => $import->getGroupId(),
                        'address' => $data[0],
                        'name' => !empty($data[1]) ? $data[1] : "N/A" ,
                        'confirmed' => !empty($data[2]) ? $data[2] : 0,
                        'status' => 1,
                    ];
                }

                echo "Loaded strings: ".count($emailsList).PHP_EOL;

                // Сохраняем в бд
                if(count($emailsList) > 0){

                    $count = 0;

                    $phql = true;

                    $emailsList = array_chunk($emailsList, 5, true);

                    foreach($emailsList as $mails){

                            foreach($mails as $mail){

                                if($phql){
                                    if($this->insertToPHqlAndModels($mail, true, $import)) continue;
                                } else {
                                    if($this->insertToPHqlAndModels($mail, false, $import)) continue;
                                }

                                // Сохраняем данные очереди
                                if($import->setCurrent($import->getCurrent()+1)->save() == false){
                                    echo "Warning: ".$import->getMessages().PHP_EOL;
                                }
                            }

                        usleep(rand(5000, 10000));

                        $count++;
                    }

                    if($count > 0)
                        $result = true;
                }

            } else {
                echo "Error: Permission denied on file or directory {$csv}".PHP_EOL;
            }

        } else {
            echo "Error: file not found {$csv}".PHP_EOL;
        }

        return $result;
    }

    /**
     * Вставка в БД с помощью технологий PHQL или Models
     *
     * @param $mail
     * @param $phql
     * @param $import
     * @return bool
     */
    private function insertToPHqlAndModels($mail, $phql, $import){

        $skip = false;

        if($phql){

            // Формируем строку вставки
            $insert = "INSERT INTO Mailer\Models\EmailList (userId, groupId, address, name, confirmed, status) VALUES (:userId:, :groupId:, :address:, :name:, :confirmed:, :status:)";
//
            // With bound parameters
            $manager = $this->modelsManager->createQuery($insert);

            /** @var Status $status */
            $status = $manager->execute($mail);

            if (!$status->success()){

                /** @var Message $message */
                foreach ($status->getMessages() as $message) {
                    echo "Warning : {$message->getMessage()}".PHP_EOL;
                }

                $skip = true;
            }

        } else {

            // Создаём объект
            $email = new EmailList();

            // Подготавливаем данные
            $email->assign($mail);

            // Сохраняем данные
            if (!$email->create()) {

                // Выводим ошибки
                /** @var Message $message */
                foreach ($email->getMessages() as $message) {
                    echo "Warning : {$message->getMessage()}".PHP_EOL;
                }

                $skip = true;
            }
        }

        return $skip;
    }

    /**
     * Handle a queued e-mail message job
     *
     * @param \Phalcon\Queue\Beanstalk\Job $job
     * @param array                        $data
     */
    public function handleQueuedParse($job, $data)
    {

        // по умолчанию не пропускаем задачу
        $result = false;

        // Получаем id задачи в очереди
        $jobId = $job->getId();

        $import = EmailImports::findFirst("id = {$data['id']}  AND workerPid = 0  AND status = 2");

        // Проверка
        if($import){

            echo "Catch jobId: {$jobId}".PHP_EOL;

            // Лочим задачу
            if($import->setWorkerPid(posix_getpid())->setStatus("added")->update()){
                $result = $this->parseFile($import);
            }

        } else {
            echo "Skipping job Id: {$jobId}".PHP_EOL;
        }

        $job->delete();


        if($result){

            $import->setStatus("done")->update();

            echo "Done jobId: {$jobId}".PHP_EOL;

        } else {

            $import->setStatus("cancel")->update();

            echo "Error parse jobId: {$jobId}".PHP_EOL;
        }
    }

    /**
     * Queue a new e-mail message for sending
     *
     * @param                 $id
     * @param \Closure|string $callback
     * @return mixed
     */
    public function addToQueueParse($id, $callback = null)
    {
        $callback = $this->buildQueueCallable($callback);

        $this->queue->choose('parsing');

        /**
         * Описание опций тут!
         * @link http://docs.phalconphp.ru/ru/latest/reference/queue.html
         */
        $options = [
            'priority' => rand(100, 250),
            'delay'    => rand(5, 15),
            'ttr'      => (3600 * rand(2, 24)) // 86400 - 1 сутки
        ];

        return $this->queue->put(json_encode([
            'job' => 'parsing:handleQueuedParse',
            'data' => [
                'id' => $id,
                'callback' => $callback,
            ],
        ]), $options);
    }

    /**
     * Build the callable for a queued e-mail job
     *
     * @param mixed $callback
     *
     * @return mixed
     */
    protected function buildQueueCallable($callback)
    {
        if (!$callback instanceof Closure) return $callback;

        return serialize(new SerializableClosure($callback));
    }

}
