<?php
namespace Mailer\Controllers;

use Mailer\Forms\CategoriesMessagesForm;
use Phalcon\Mvc\Model\Criteria;
use Mailer\Models\CategoryMessages;
use Mailer\Models\Users;
use Phalcon\Flash;
use Phalcon\Tag;

use Phalcon\Paginator\Adapter\Model as Paginator;

/**
 * Отображение главной страницы.
 */
class CategoriesController extends ControllerBase
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

        $numberPage = 1;

        if ($this->request->isPost()) {
            $query = Criteria::fromInput(
                $this->di,
                'Mailer\Models\CategoryMessages',
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

        $categories = CategoryMessages::find($parameters);
        if(count($categories) == 0 ){
            $this->flash->warning("У вас нет ни одной категории!");
        }

        $paginator = new Paginator([
            "data"  => $categories,
            "limit" => 10,
            "page"  => $numberPage
        ]);

        $this->view->setVar('users', Users::find('active = 1'));
        $this->view->setVar('page', $paginator->getPaginate());
    }

    /**
     * Создание
     */
    public function createAction()
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        if ($this->request->isPost()) {

            $category = new CategoryMessages();

            if($this->auth->isPriveleged()){
                $userId = $this->request->getPost('user', 'int');
            } else {
                $userId = $this->user->id;
            }

            $category->assign([
                'userId' => $userId ,
                'name' => $this->request->getPost('name', 'striptags'),
                'description' => $this->request->getPost('description', 'striptags'),
                'status' => $this->request->getPost('status', 'int')
            ]);

            if (!$category->save()) {
                $this->flash->error($category->getMessages());
            } else {

                $this->flash->success("Категория успешно создана!");

                Tag::resetInput();
            }
        }

        $this->view->setVar('form', new CategoriesMessagesForm(null));
    }

    /**
     * Редактирование по ID
     */
    public function editAction($id)
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        $category = CategoryMessages::findFirstById($id);
        if (!$category) {
            $this->flash->error("Категория не найдена!");
            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }

        if ($this->request->isPost()) {

            if($this->auth->isPriveleged()){
                $userId = $this->request->getPost('user', 'int');
            } else {
                $userId = $this->user->id;
            }

            $category->assign([
                'userId' => $userId ,
                'name' => $this->request->getPost('name', 'striptags'),
                'description' => $this->request->getPost('description', 'striptags'),
                'status' => $this->request->getPost('status', 'int')
            ]);


            if (!$category->save()) {
                $this->flash->error($category->getMessages());
            } else {

                $this->flash->success("Данные категории были успешно обновлены!");

                Tag::resetInput();
            }
        }

        $this->view->setVar('category', $category);
        $this->view->setVar('form', new CategoriesMessagesForm($category, [
            'edit' => true
        ]));
    }


    /**
     * Удаление
     *
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function deleteAction($id) {

        $category = CategoryMessages::findFirstById($id);
        if (!$category) {
            $this->flash->error("Категория не найдена!");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if (!$category->delete()) {
            $this->flash->error($category->getMessages());
        } else {
            $this->flash->success("Категория удалена!");
        }

        return $this->response->redirect('/categories');
    }

}
