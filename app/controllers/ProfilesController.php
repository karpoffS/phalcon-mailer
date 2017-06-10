<?php
namespace Mailer\Controllers;

use Phalcon\Tag;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Mailer\Forms\ProfilesForm;
use Mailer\Models\Profiles;

/**
 * Mailer\Controllers\ProfilesController
 * CRUD для управления профилями
 */
class ProfilesController extends ControllerBase
{

    /**
     * Действие по умолчанию.
     * Проверка подлинности.
     * Установка шаблона (layouts/private.volt)
     */
    public function initialize()
    {
        $this->view->setTemplateBefore('private');

        parent::initialize();
    }

    /**
     * Действие по умолчанию, показывает форму поиска профилей
     */
    public function indexAction()
    {
        $this->persistent->conditions = null;
        $this->view->form = new ProfilesForm();
    }

    /**
     * Поиск профилей
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Mailer\Models\Profiles', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = array();
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $profiles = Profiles::find($parameters);
        if (count($profiles) == 0) {

            $this->flash->notice("The search did not find any profiles");

            return $this->dispatcher->forward(array(
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $profiles,
            "limit" => 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Создает новый профиль
     */
    public function createAction()
    {
        if ($this->request->isPost()) {

            $profile = new Profiles();

            $profile->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'active' => $this->request->getPost('active')
            ));

            if (!$profile->save()) {
                $this->flash->error($profile->getMessages());
            } else {
                $this->flash->success("Profile was created successfully");
            }

            Tag::resetInput();
        }

        $this->view->form = new ProfilesForm(null);
    }

    /**
     * Изменение существующего профиля по ID
     *
     * @param int $id
     */
    public function editAction($id)
    {
        $profile = Profiles::findFirstById($id);
        if (!$profile) {
            $this->flash->error("Profile was not found");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if ($this->request->isPost()) {

            $profile->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'active' => $this->request->getPost('active')
            ));

            if (!$profile->save()) {
                $this->flash->error($profile->getMessages());
            } else {
                $this->flash->success("Profile was updated successfully");
            }

            Tag::resetInput();
        }

        $this->view->form = new ProfilesForm($profile, array(
            'edit' => true
        ));

        $this->view->profile = $profile;
    }

    /**
     * Удаление профиля по ID
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $profile = Profiles::findFirstById($id);
        if (!$profile) {

            $this->flash->error("Profile was not found");

            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if (!$profile->delete()) {
            $this->flash->error($profile->getMessages());
        } else {
            $this->flash->success("Profile was deleted");
        }

        return $this->dispatcher->forward(array(
            'action' => 'index'
        ));
    }
}
