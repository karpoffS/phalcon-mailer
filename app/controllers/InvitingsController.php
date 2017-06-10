<?php

namespace Mailer\Controllers;

use Mailer\Mailings\Mailer;
use Mailer\Models\QueueList;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Mailer\Forms\InviteCodeForm;
use Phalcon\Mvc\Model\Criteria;
use Mailer\Models\InviteCode;
use Mailer\Models\Users;
use Phalcon\Queue\Beanstalk;
use Phalcon\Tag;


class InvitingsController extends ControllerBase
{

    /**
     * Установка шаблона (layouts/private.volt)
     */
    public function initialize() {

        $this->view->setTemplateBefore('private');
        parent::initialize();
    }

    public function indexAction()
    {
        $this->persistent->conditions = null;

        $numberPage = 1;

        if ($this->request->isPost()) {
            $query = Criteria::fromInput(
                $this->di,
                'Mailer\Models\InviteCode',
                $this->request->getPost()
            );
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = [];
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $parameters["order"] = 'id';

        $invites = InviteCode::find($parameters);
        if(count($invites) == 0 ){
            $this->flash->warning("Список приглашений пуст!");
        }

        $paginator = new Paginator([
            "data"  => $invites,
            "limit" => 10,
            "page"  => $numberPage
        ]);

        $this->view->setVar('users', Users::find('active = 1'));

        $this->view->setVar('page', $paginator->getPaginate());
    }


    /**
     * Рассылка приглашений
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function mailingAction() {

        /** @var InviteCode $queue */
        $invites = InviteCode::find("status = 0");

        if(count($invites) == 0 ){

            $this->flash->warning("Список приглашений пуст рассылать нечего!");
            return $this->dispatcher->forward([ 'action' => 'index' ]);

        } else {

            /**
             * Описание опций тут!
             * @link http://docs.phalconphp.ru/ru/latest/reference/queue.html
             */
            $options = [
                'priority' => 1,
                'delay'    => 10,
                'ttr'      => (3600 * 24) // 86400 - 1 сутки
            ];

            /** @var Mailer $mailer */
            $mailer = $this->getDI()->get('mailer');

            /** @var InviteCode $invite */
            foreach($invites as $invite){

                // Добавляем в очередь
                $mailer->queue('emailTemplates/invite', [
                    // Переменные для передачи в шаблон
                    'publicUrl' => $this->getDI()->get('config')->application->publicUrl,
                    'inviteCode' => $invite->getCode(),
                    'inviteUrl' => '/invite/' . $invite->getCode() . '/' . $invite->getEmail()
                ], [
                    // Переменные для передачи в Closure
                    'address' => [ $invite->getEmail() => $invite->getName() ],
                    'subject' => "Приглашение на тестирование!"
                ], $options);
            }
        }

        return $this->dispatcher->forward([ 'action' => 'index' ]);

    }

    /**
     * Создание
     */
    public function createAction()
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        if ($this->request->isPost()) {

            $invite = new InviteCode();

            $invite->assign([
                'email' => $this->request->getPost('email', 'striptags')
            ]);

            if (!$invite->save()) {
                $this->flash->error($invite->getMessages());
            } else {

                $this->flash->success("Приглашение успешно создано!");

                Tag::resetInput();
            }
        }

        $this->view->setVar('form', new InviteCodeForm(null));
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function deleteAction($id) {

        $invite = InviteCode::findFirstById($id);
        if (!$invite) {
            $this->flash->error("Код приглашения не найден!");

            return $this->dispatcher->forward([ 'action' => 'index' ]);

        }

        if (!$invite->delete()) {

            $this->flash->error($invite->getMessages());

        } else {

            $this->flash->success("Код приглашения удалён!");
        }

        return $this->dispatcher->forward([ 'action' => 'index' ]);
    }

}

