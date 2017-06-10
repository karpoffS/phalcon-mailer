<?php

namespace Mailer\Scheduler;

use Closure;

use Jeremeamia\SuperClosure\SerializableClosure;
use Mailer\Models\EmailList;
use Mailer\Models\QueueList;
use Mailer\Models\Queuing;
use Phalcon\Mvc\User\Component;

use Phalcon\Queue\Beanstalk;


class SchedulerService  extends Component {


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
     * Создаёт кучу в рассыльщике
     *
     * @param \Phalcon\Queue\Beanstalk\Job $job
     * @param array                        $data
     */
    public function handleQueuedSchedule($job, array $data)
    {

        // Труба для новых задач
        $tube = 'mailer';

        // по умолчанию не пропускаем задачу
        $skipJob = false;

        // Добавлены задания в очередь
        $isAddToQueue = false;

        // Получаем id текущей задачи в очереди
        $jobId = $job->getId();

        // Если задача и не занята воркером и не блокирована и не выполнена

        /** @var Queuing $queue */
        $queue = Queuing::findFirst("jobId = '{$jobId}' AND workerPid = 0  AND status = 0 AND lock = 0");

        // Если нашли строчку в БД
        if($queue){

            echo "Catch jobId: {$jobId}".PHP_EOL;

            // Лочим задачу
            if($queue->setWorkerPid(posix_getpid())->setStatus(1)->setLock(1)->update() == false){

                $skipJob = true;

            } else {

                $count = 0;

                $listEmails = EmailList::find(["groupId = {$queue->getGroupId()}", "order" => "id"]);

                if(!$listEmails){

                    echo "Error: К сожалению, пользователь {$queue->getUserId()} удалил список рассылки!";

                    $skipJob = true;

                } else {

                    echo "Choice new tube '{$tube}' to beantalks:";
                    // Подключаемся к тубе
                    $error = $this->queue->choose($tube);

                    if(is_string($error)){
                        echo $error;
                    } else {
                        echo "Ok".PHP_EOL;
                    }

                    echo "Load list emails to queue: ".count($listEmails).PHP_EOL;

                    /** @var EmailList $email */
                    foreach($listEmails as $email){

                        // Формируем для записи массив дынных строки
                        $assign = [
                            'queueId' => $queue->getId(),
                            'userId' => $queue->getUserId(),
                            'messageId' => $queue->getMessageId(),
                            'categoryId' => $queue->getCategoryId(),
                            'groupId' => $queue->getGroupId(),
                            'emailId' => $email->getId()
                        ];

                        // получаем новый id задачи в очереди
                        $newId = $this->queue->put(json_encode([
                            'job' => 'mailer:handleQueuedMailerSender',
                            'data' => $assign
                        ]));

                        // Если получили новый id
                        if($newId){

                            /** @var QueueList $list */
                            $list = new QueueList();

                            // Сохраняем новый jobId
                            $assign['jobId'] = $newId;

                            // Сохраняем в БД
                            $list->assign($assign);

                            // Если какая-то ошибка то выводим
                            if($list->save() == false){

                                foreach($list->getMessages() as $message){
                                    echo "Error: {$message}".PHP_EOL;
                                }
                            }

                            $count++;
                       }
                    }
                }

                if($count > 0){
                    $isAddToQueue = true;
                }

            }
        }


        // Если отправили то сообщаем об отправке
        if($isAddToQueue){

            echo "Created jobs to queue is tube '{$tube}' a count: {$count}".PHP_EOL;

            // Удаляем с очереди
            $job->delete();

            // Пометили как запущенное
            $queue->setStatus(1)->setLock(0);

            if($queue->update() == false){
                echo "Error: ".$queue->getMessages().PHP_EOL;
            }

            echo "Done jobId: {$jobId}".PHP_EOL;

        } elseif($isAddToQueue == false) { // Иначе сообщаем о пропуске

            $job->delete();

            // Помечаем задучу как снятую с очереди и снимаем блокировку
            $queue->setStatus(3)->setLock(0);

            if($queue->update() == false){
                echo "Error: ".$queue->getMessages().PHP_EOL;
            }

            echo "Error sending jobId: {$jobId}".PHP_EOL;

        } elseif($skipJob) {

            $job->delete();

            echo "Skipping job Id: {$jobId}".PHP_EOL;
        }
    }

    /**
     * Queue a new e-mail message for sending
     *
     * @param                 $id
     * @param \Closure|string $callback
     * @return mixed
     */
    public function addToQueueSchedule($id, $callback = null)
    {
        $callback = $this->buildQueueCallable($callback);

        $this->queue->choose('scheduling');

        /**
         * Описание опций тут!
         * @link http://docs.phalconphp.ru/ru/latest/reference/queue.html
         */
        $options = [
            'priority' => 1,
            'delay'    => 2,
            'ttr'      => (3600 * 24) // 86400 - 1 сутки
        ];

        return $this->queue->put(json_encode([
            'job' => 'scheduler:handleQueuedSchedule',
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