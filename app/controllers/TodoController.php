<?php
namespace Mailer\Controllers;


class TodoController extends ControllerBase
{

    public function initialize()
    {
        $this->view->setTemplateBefore('private');
        parent::initialize();
    }

    /**
     * Действие по умолчанию.
     * Установка шаблона (layouts/public.volt)
     */
    public function indexAction()
    {

    }
}
