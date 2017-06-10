<?php

namespace Mailer\Mailings;

use Closure;
use Mailer\Auth\Exception;
use Mailer\Models\EmailList;
use Mailer\Models\MessagesTemplates;
use Mailer\Models\QueueList;
use Mailer\Models\Queuing;
use Phalcon\DiInterface;
use Swift_Mailer;
use Swift_Message;
use Jeremeamia\SuperClosure\SerializableClosure;
use Phalcon\DI\InjectionAwareInterface;
use Phalcon\Queue\Beanstalk;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\Query\Status;


/**
 * Class Mailer
 *
 * @package Mailer
 */
class Mailer implements InjectionAwareInterface
{
    /**
     * The view environment instance
     *
     * @var \Phalcon\Mvc\View
     */
    protected $view;

    /**
     * The Swift Mailer instance
     *
     * @var Swift_Mailer
     */
    protected $swift;

    /**
     * The global from address and name
     *
     * @var array
     */
    protected $from;

    /**
     * Array of failed recipients
     *
     * @var array
     */
    protected $failedRecipients = [];

    /**
     * The Benastalk queue instance
     *
     * @var \Phalcon\Queue\Beanstalk
     */
    protected $queue;

    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * Create a new Mailer instance
     *
     * @param \Phalcon\Mvc\View $view
     * @param Swift_Mailer      $swift
     */
    public function __construct($view, Swift_Mailer $swift)
    {
        $this->view = $view;
        $this->swift = $swift;
    }

    /**
     * Set the global from address and name
     *
     * @param string $address
     * @param string $name
     */
    public function alwaysFrom($address, $name = null)
    {
        $this->from = compact('address', 'name');
    }

    /**
     * Send a new message when only a plain part
     *
     * @param string $view
     * @param array  $data
     * @param mixed  $callback
     *
     * @return int
     */
    public function plain($view, array $data, $callback)
    {
        return $this->send(['text' => $view], $data, $callback);
    }

    /**
     * Send a new message using a view
     *
     * @param string|array   $view
     * @param array          $data
     * @param Closure|string $callback
     *
     * @return int
     */
    public function send($view, array $data, $callback)
    {
        // First we need to parse the view, which could either be a string or an array
        // containing both an HTML and plain text versions of the view which should
        // be used when sending an e-mail. We will extract both of them out here.
        list($view, $plain) = $this->parseView($view);

        $data['message'] = $message = $this->createMessage();

        $this->callMessageBuilder($callback, $message);

        // Once we have retrieved the view content for the e-mail we will set the body
        // of this message using the HTML type, which will provide a simple wrapper
        // to creating view based emails that are able to receive arrays of data.
        $this->addContent($message, $view, $plain, $data);

        $message = $message->getSwiftMessage();

        return $this->sendSwiftMessage($message);
    }

    /**
     * Add the content to a given message
     *
     * @param Message $message
     * @param string  $view
     * @param string  $plain
     * @param array   $data
     */
    protected function addContent(Message $message, $view, $plain, $data)
    {
        if (isset($view)) {
            $message->setBody($this->render($view, $data), 'text/html');
        }

        if (isset($plain)) {
            $message->addPart($this->render($plain, $data), 'text/plain');
        }
    }

    /**
     * Parse the given view name or array
     *
     * @param string|array $view
     *
     * @return array
     */
    protected function parseView($view)
    {
        if (is_string($view)) return [$view, null];

        // If the given view is an array with numeric keys, we will just assume that
        // both a "pretty" and "plain" view were provided, so we will return this
        // array as is, since must should contain both views with numeric keys.
        if (is_array($view) && isset($view[0])) {
            return $view;
        }

        // If the view is an array, but doesn't contain numeric keys, we will assume
        // the the views are being explicitly specified and will extract them via
        // named keys instead, allowing the developers to use one or the other.
        elseif (is_array($view)) {
            return [
                array_get($view, 'html'),
                array_get($view, 'text'),
            ];
        }

        throw new \InvalidArgumentException("Invalid view.");
    }

    /**
     * Send a Swift Message instance
     *
     * @param Swift_Message $message
     *
     * @return int
     */
    public function sendSwiftMessage(Swift_Message $message)
    {
        return $this->swift->send($message);
    }

    /**
     * Call the provided message builder
     *
     * @param $callback
     * @param $message
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function callMessageBuilder($callback, $message)
    {
        if ($callback instanceof Closure) {
            return call_user_func($callback, $message);
        }

        throw new \InvalidArgumentException("Callback is not valid.");
    }

    /**
     * Create a new message instance
     *
     * @return Message
     */
    protected function createMessage()
    {
        $message = new Message(new Swift_Message);


        // If a global from address has been specified we will set it on every message
        // instances so the developer does not have to repeat themselves every time
        // they create a new message. We will just go ahead and push the address.
        if (isset($this->from['address'])) {
            $message->from($this->from['address'], $this->from['name']);
        }

        return $message;
    }

    /**
     * Render the given view
     *
     * @param string $view
     * @param array  $data
     *
     * @return string
     */
    protected function render($view, $data)
    {
        ob_start();
        $this->view->partial($view, $data);
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Get the view environment instance
     *
     * @return \Phalcon\Mvc\View
     */
    public function getViewEnvironment()
    {
        return $this->view;
    }

    /**
     * Get the Swift Mailer instance
     *
     * @return Swift_Mailer
     */
    public function getSwiftMailer()
    {
        return $this->swift;
    }

    /**
     * Set the Swift Mailer instance
     *
     * @param Swift_Mailer $swift
     */
    public function setSwiftMailer($swift)
    {
        $this->swift = $swift;
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

    /**
     * Handle a queued e-mail message job
     *
     * @param \Phalcon\Queue\Beanstalk\Job $job
     * @param array                        $data
     */
    public function handleQueuedMessage($job, $data)
    {

        $jobId = $job->getId();

        echo "Catch jobId: {$jobId}".PHP_EOL;

        $from = $data['from'];

        $job->delete();

        $isSend = $this->send($data['view'], $data['data'], function($message) use($from) {
            $message->to($from['address']);
            $message->subject($from['subject']);
        });

        if($isSend){
            echo "Done jobId: {$jobId}".PHP_EOL;
        } else {
            echo "Error jobId: {$jobId}".PHP_EOL;
        }
    }

    /**
     * Get the true callable for a queued e-mail message
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function getQueuedCallable(array $data)
    {
        if (str_contains($data['callback'], 'SerializableClosure')) {
            return with(unserialize($data['callback']))->getClosure();
        }

        return $data['callback'];
    }

    /**
     * Queue a new e-mail message for sending
     *
     * @param string|array    $view
     * @param array           $data
     * @param array           $from
     * @param array|null      $options
     *
     * @return mixed
     */
    public function queue($view, array $data, array $from, array $options = null)
    {
        $this->queue->choose('mailer');

        if(!empty($options)){

            return $this->queue->put(
                json_encode([
                    'job' => 'mailer:handleQueuedMessage',
                    'data' => [
                        'view' => $view,
                        'data' => $data,
                        'from' => $from,
                    ],
                ]), $options
            );
        }

        return $this->queue->put(
            json_encode([
                'job' => 'mailer:handleQueuedMessage',
                'data' => [
                    'view' => $view,
                    'data' => $data,
                    'from' => $from,
                ],
            ])
        );

    }

    /**
     * Получаем путь
     *
     * @param $userId
     * @return string
     */
    private function getPath($userId){

        return 'emailTemplates/users/' . $userId . DIRECTORY_SEPARATOR;
    }

    /**
     * Получаем имя файла с расширением
     *
     * @param        $data
     * @param string $ext
     * @return string
     */
    private function getFilename($data, $ext = ".volt") {

        // Вычесляем хеш файла
        $hash = md5($data['userId'] . $data['categoryId'] . $data['messageId']);

        return $hash . $ext;
    }

    /**
     * Создаёт кучу в рассыльщике
     * @param Queuing $queue
     * @return bool|int
     */
    public function addToQueueInMailer(Queuing $queue)
    {

        // Подключаемся к тубе
        $this->queue->choose('mailer');

        $listEmails = EmailList::find(["groupId = {$queue->getGroupId()}", "order" => "id"]);

        if(!$listEmails){

            $this->getDI()->get('flash')->warning("К сожалению, вы удалили список рассылки!");

            return $this->getDI()->get('dispatcher')->forward([ 'action' => 'index']);
        }

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

            // отправляем в очередь
            $jobId = $this->queue->put(json_encode([
                'job' => 'mailer:handleQueuedMailerSender',
                'data' => $assign
            ]));

            if($jobId){

                /** @var QueueList $list */
                $list = new QueueList();

                $assign['jobId'] = $jobId;

                // Сохраняем в БД
                $list->assign($assign);

                if($list->save() == false){

                    $this->getDI()->get('flash')->error($list->getMessages());
                    break;
                }
            }
        }

        // Пометили как запущенное
        $queue->setStatus(1)->save();

    }

    /**
     * Handle a queued e-mail message job
     *
     * @param \Phalcon\Queue\Beanstalk\Job $job
     * @param array                        $data
     */
    public function handleQueuedMailerSender($job, $data)
    {

        // по умолчанию не пропускаем задачу
        $skipJob = false;

        $isSend = false;

        // Получаем id задачи в очереди
        $jobId = $job->getId();

        // Если задача и не занята воркером и не блокирована и не выполнена
        /** @var QueueList $queue */
        $queue = QueueList::findFirst("jobId = '{$jobId}' AND workerPid = 0  AND status = 0 AND lock = 0");

        // Если нашли строчку в БД
        if($queue){

            echo "Catch jobId: {$jobId}".PHP_EOL;

            // Лочим задачу
            if($queue->setWorkerPid(posix_getpid())->setLock(1)->save() == false){

                $skipJob = true;

            } else {
                /** @var EmailList $email */
                $email = EmailList::findFirstById($queue->getEmailId());

                /** @var MessagesTemplates $message */
                $message = MessagesTemplates::findFirstById($queue->getMessageId());

                // Формируем кому отправлять
                $from = [
                    'address' => $email->getName() !== "N/A" ? [ $email->getAddress() => $email->getName() ] : $email->getAddress(),
                    'subject' => $message->getSubject()
                ];

                echo "Sending at: ". $email->getAddress()." the userId ".$email->getUserId().PHP_EOL;

                // Формируем путь к шаблону
                $messageTemplate = $this->getPath($queue->getUserId()) .
                    $this->getFilename([
                        'userId' => $queue->getUserId(),
                        'messageId' => $queue->getMessageId(),
                        'categoryId' => $queue->getCategoryId()
                    ], "");

                // Данные для volt шаблонизатора {{ username }}
                $dataVolt = [
                    'username' => $email->getName()
                ];

//                try{
//
//                    // Отправляем письмо
//                    $isSend = $this->send($messageTemplate, $dataVolt, function($message) use($from) {
//                        $message->to($from['address']);
//                        $message->subject($from['subject']);
//                    });
//
//                    // Получаем транспорт
//                    $transport = $this->getSwiftMailer()->getTransport();
//
//                    //  Если запущен, то останавливаем
//                    if($transport->isStarted()){
//                        $transport->stop();
//                    }
//
//                } catch(\Exception $e){
//                    $queue->setErrors($e->getMessage());
//                }

                $isSend = true;
            }
        }

        // Если отправили то сообщаем об отправке
        if($isSend){

            // Удаляем с очереди
            $job->delete();

            // Помечаем здание как выполненное
            $queue->setStatus('sends')->setLock(0);

            if($queue->update() == false){
                echo "Error: ".$queue->getMessages().PHP_EOL;
            }

            /** @var Queuing $Queuing */
            $Queuing = Queuing::findFirstById($queue->getQueueId());

            // Сохраняем данные очереди
            $Queuing->setCurrent($Queuing->getCurrent()+1)->setStatus(1);

            if($Queuing->update() == false){
                echo "Error: ".$queue->getMessages().PHP_EOL;
            }

            echo "Done jobId: {$jobId}".PHP_EOL;

        } elseif($isSend == false) { // Иначе сообщаем о пропуске

            $job->delete();

            // Помечаем задучу как снятую с очереди и снимаем блокировку
            $queue->setStatus('cancel')->setLock(0);

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
     * Get the array of failed recipients
     *
     * @return array
     */
    public function failures()
    {
        return $this->failedRecipients;
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
     * Sets the dependency injector
     *
     * @param mixed $dependencyInjector
     */
    public function setDI(DiInterface $dependencyInjector)
    {
        $this->di = $dependencyInjector;
    }

    /**
     * Returns the internal dependency injector
     *
     * @return DiInterface
     */
    public function getDI()
    {
        return $this->di;
    }
}
