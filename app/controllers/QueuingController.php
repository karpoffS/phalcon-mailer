<?php
namespace Mailer\Controllers;


use Mailer\Forms\QueuingForm;
use Mailer\Mailings\Mailer;
use Mailer\Models\EmailList;
use Mailer\Models\MessagesTemplates;
use Mailer\Models\QueueList;
use Mailer\Models\Queuing;
use Mailer\Models\Users;
use Mailer\Scheduler\SchedulerService;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Flash;
use Phalcon\Tag;

use Phalcon\Paginator\Adapter\Model as Paginator;

/**
 * Группы адресов
 */
class QueuingController extends ControllerBase
{

    /**
     *  @var \Mailer\Models\Users
     */
    protected $user;


    /**
     * Установка шаблона (layouts/private.volt)
     */
    public function initialize() {

        $this->user = $this->auth->getUser();

        $this->view->setTemplateBefore('private');
        parent::initialize();
    }

    /**
     * Действие по умолчанию.
     */
    public function indexAction()
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        $numberPage = 1;

        if ($this->request->isPost()) {
            $query = Criteria::fromInput(
                $this->di,
                'Mailer\Models\Queuing',
                $this->request->getPost()
            );
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        if($this->auth->isPriveleged()){
            $parameters = [];
        } else {
            $parameters = [
                'userId = '.$this->user->id
            ];
        }

        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $queuings = Queuing::find($parameters);
        if(count($queuings) == 0 ){
            $this->flash->warning("У вас нет ни одной очереди!");
        }

        $paginator = new Paginator([
            "data"  => $queuings,
            "limit" => 50,
            "page"  => $numberPage
        ]);

        $this->view->setVar('page', $paginator->getPaginate());
    }

    public function listAction()
    {
        ini_set('memory_limit', '512M');


//        $this->persistent->conditions = null;
//        $this->persistent->searchParams = null;

        $numberPage = 1;

        if ($this->request->isPost()) {
            $query = Criteria::fromInput(
                $this->di,
                'Mailer\Models\QueueList',
                $this->request->getPost()
            );
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

//        if($this->auth->isPriveleged()){
//            $parameters = [];
//        } else {
            $parameters = [
                'userId = '.$this->user->id,
                "order" => "id ASC"
            ];
//        }

        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $parameters["order"] = "id ASC";

        $list = QueueList::find($parameters);

        if(count($list) == 0 ){
            $this->flash->warning("У вас нет ни одного выполненого задания!");
        }

        $paginator = new Paginator([
            "data"  => $list,
            "limit" => 100,
            "page"  => $numberPage
        ]);

        $this->view->setVar('page', $paginator->getPaginate());
        $this->view->setVar('users', Users::find());
        $this->view->setVar('filter', Queuing::find());
        $this->view->setVar('parameters', $parameters);
    }

    /**
     * Создание задания
     */
    public function createAction()
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        if ($this->request->isPost()) {

            $queue = new Queuing();

            // userId
            $userId = $this->user->id;

            // Id сообщения
            $messageId =  $this->request->getPost('messageId', 'int');

            /** @var MessagesTemplates $message */
            $message = MessagesTemplates::findFirstById($messageId);

            // Группа emails
            $groupId =  $this->request->getPost('groupId', 'int');

            /** @var EmailList $emails */
            $emails = EmailList::count("userId = '{$userId}' AND groupId = '{$groupId}' ");

            $assing = [
                'jobId' => 0,
                'workerPid' => 0,
                'userId' => $userId,
                'categoryId' => $message->categoryId,
                'messageId' => $messageId,
                'groupId' => $groupId,
                'totals' => $emails
            ];

            // Собраем данные
            $queue->assign($assing); // 0 - в очереди , 1 - выполняется, 2 - выполнена, 3 - ошибки

            // Сохраняем
            if (!$queue->save()) {

                $this->flashSession->error($queue->getMessages());

            } else {

                $this->flashSession->success("Очередь успешно создана!");

                Tag::resetInput();



                return $this->response->redirect("/queuing");

            }
        }

        $this->view->setVar('form', new QueuingForm(null));
    }


    /**
     * Удаление задания
     *
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function deleteAction($id) {

        /** @var Queuing $queue */
        $queue = Queuing::findFirstById($id);

        if (!$queue) {
            $this->flashSession->error("Задание не найдено!");

            return $this->response->redirect("/queuing");
        }

        if (!$queue->delete()) {
            $this->flashSession->error($queue->getMessages());
        } else {
            $this->flashSession->success("Задание удалено!");
        }

        return $this->response->redirect("/queuing");
    }


    /**
     * Постановка в очередь заданий
     *
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function playAction($id) {

        /** @var Queuing $queue */
        $queue = Queuing::findFirst(" id = '{$id}' AND status=0 ");

        if (!$queue) {

            $this->flashSession->warning("Такого задания не найдено в базе!");

            return $this->response->redirect("/queuing");

        } else {

            /** @var SchedulerService $scheduler */
            $scheduler = $this->getDI()->get('scheduler');

//            /** @var Mailer $mailer */
//            $mailer = $this->getDI()->get('mailer');

            // Постановка в очередь заданий
//            $mailer->addToQueueInMailer($queue);

            if($jobId = $scheduler->addToQueueSchedule($queue->getId())){

                if($queue->setJobId($jobId)->save()){
                    $this->flashSession->notice("jobId - {$jobId}!");
                    $this->flashSession->notice("Список поставлен в очередь на обработку!");
                } else {
                    $this->flashSession->error("Что-то пошло не так и список не поставлен в обработку!");
                }
            }

        }

        return $this->response->redirect("/queuing");
    }

}
