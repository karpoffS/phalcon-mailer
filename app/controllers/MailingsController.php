<?php
namespace Mailer\Controllers;

use Mailer\Models\Users;
use Phalcon\Flash;
use Phalcon\Tag;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Mailer\Forms\MailingsForm;
use Mailer\Models\EmailList;
use Mailer\Models\EmailImports;
use Mailer\Models\EmailGroups;

/**
 * Mailer\Controllers\MailingsController
 * CRUD для управления пользователями
 */
class MailingsController extends ControllerBase
{

    /**
     *  @var \Mailer\Models\Users
     */
    protected $user;

    /**
     * @var \Mailer\Flash\Direct
     */
    protected $flash;

    /**
     *
     */
    public function initialize()
    {
        $this->flash = $this->getDI()->get('flash')->setType('alert');
        $this->user = $this->getDI()->get('auth')->getUser();
        $this->view->setTemplateBefore('private');
        parent::initialize();
    }


    /**
     * Получить путь с пользовательской папкой
     * @return string
     */
    public function getPath() {
        return $this->config->application->uploadsDir."/".$this->user->id;
    }

    /**
     * Действие по умолчанию, показывает таблицу
     */
    public function indexAction()
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Mailer\Models\EmailList', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        if($this->auth->isPriveleged()){
            $parameters = [];
        } else {
            $parameters = ['userId = '.$this->user->id ];
        }

        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $parameters["order"] = "address";

        $emails = EmailList::find($parameters);
        if(count($emails) == 0 ){

            if($this->auth->isPriveleged()){
                $this->view->setVar('showImportLink', false);
                $this->flashSession->warning("По вашему запросу ничего не найдено!");
            } else {
                $this->view->setVar('showImportLink', true);
                $this->flashSession->warning("Загрузите хотя бы один список адресов!");
            }

        } else {
            $this->view->setVar('showImportLink', false);
        }

        $paginator = new Paginator([
            "data"  => $emails,
            "limit" => 10,
            "page"  => $numberPage
        ]);

        $emailGroups = EmailGroups::find([
            "status=1 and userId=".$this->user->id
        ]);

        if ($this->persistent->searchParams) {
            $this->view->form = new MailingsForm(null, $this->persistent->searchParams['bind']);
        } else {
            $this->view->form = new MailingsForm();
        }

        $this->view->imports = EmailImports::find("userId = '". $this->user->id ."'");
        $this->view->users = Users::find('active = 1');
        $this->view->emailgroups = $emailGroups;
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Статус обработки
     */
    public function commandAction($id){

        if($list = EmailList::findFirst("id='{$id}'")){

            if($list->getStatus() === 0){
                $list->setStatus(1);
            } else {
                $list->setStatus(0);
            }

            if($list->save()){

                if($list->getStatus() === 0){
                    $this->flash->warning("Адресс {$list->address} включён в список рассылки!");
                } else {
                    $this->flash->warning("Адресс {$list->address} исключён из списка рассылки!");
                }

                return $this->dispatcher->forward([ 'action' => 'index' ]);
            }
        } else {

            $this->flash->error("Мы не нашли такого адреса в списке у нас для того чтобы включить/выключить его из списка текущей рассылки!");

            return $this->dispatcher->forward([ 'action' => 'index' ]);

        }

        return $this->response->redirect('/mailings');

    }


    public function deleteAction($id){
        if($list = EmailList::findFirst("id={$id}")){
            if($list->delete()){
                $this->flash->warning("Адрес удалён!");
                return $this->response->redirect('/mailings');
            }
        } else {
            $this->flash->error("Такого адреса нет в списке!");
            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }
    }
}
