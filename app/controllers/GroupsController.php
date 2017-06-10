<?php
namespace Mailer\Controllers;

use Mailer\Forms\GroupsAddressesForm;
use Phalcon\Mvc\Model\Criteria;
use Mailer\Models\EmailGroups;
use Mailer\Models\Users;
use Phalcon\Flash;
use Phalcon\Tag;

use Phalcon\Paginator\Adapter\Model as Paginator;

/**
 * Группы адресов
 */
class GroupsController extends ControllerBase
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
                'Mailer\Models\EmailGroups',
                $this->request->getPost()
            );
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

        $groups = EmailGroups::find($parameters);
        if(count($groups) == 0 ){
            $this->flash->warning("У вас нет ни одной группы!");
        }

        $paginator = new Paginator([
            "data"  => $groups,
            "limit" => 10,
            "page"  => $numberPage
        ]);

        $this->view->setVar('users', Users::find('active = 1'));
        $this->view->setVar('page', $paginator->getPaginate());
    }

    /**
     * Создание группы
     */
    public function createAction()
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        if ($this->request->isPost()) {

            $group = new EmailGroups();


            if($this->auth->isPriveleged()){
                $userId = $this->request->getPost('user', 'int');
            } else {
                $userId = $this->user->id;
            }

            $group->assign([
                'userId' => $userId ,
                'name' => $this->request->getPost('name', 'striptags'),
                'description' => $this->request->getPost('description', 'striptags'),
                'status' => $this->request->getPost('status', 'int')
            ]);

            if (!$group->save()) {
                $this->flash->error($group->getMessages());
            } else {

                $this->flash->success("Группа успешно создана!");

                Tag::resetInput();

                return $this->dispatcher->forward([ 'action' => 'index' ]);

            }
        }

        $this->view->setVar('form', new GroupsAddressesForm(null));
    }

    /**
     * Редактирование по ID
     */
    public function editAction($id)
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        $group = EmailGroups::findFirstById($id);
        if (!$group) {
            $this->flash->error("Группа не найдена!");
            return $this->dispatcher->forward([ 'action' => 'index' ]);
        }

        if ($this->request->isPost()) {

            // Заполняем
            $group->assign([
                'name' => $this->request->getPost('name', 'striptags'),
                'description' => $this->request->getPost('description', 'striptags'),
                'status' => $this->request->getPost('status', 'int')
            ]);


            if (!$group->save()) {
                $this->flash->error($group->getMessages());
            } else {

                $this->flash->success("Данные группы были успешно обновлены!");

                Tag::resetInput();
            }
        }

        $this->view->setVar('group', $group);
        $this->view->setVar('form', new GroupsAddressesForm($group, [
            'edit' => true
        ]));
    }


    /**
     * Удаление группы
     *
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function deleteAction($id) {
        $group = EmailGroups::findFirstById($id);
        if (!$group) {
            $this->flash->error("Группа не найдена!");

            return $this->dispatcher->forward([ 'action' => 'index' ]);

        }

        if (!$group->delete()) {
            $this->flash->error($group->getMessages());
        } else {
            $this->flash->success("Группа удалена!");
        }

        return $this->dispatcher->forward([ 'action' => 'index' ]);
    }

}
