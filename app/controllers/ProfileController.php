<?php
namespace Mailer\Controllers;

/**
 * Отображение главной страницы.
 */
class ProfileController extends ControllerBase
{

    /**
     * Установка шаблона (layouts/private.volt)
     */
    public function initialize() {
        $this->view->setTemplateBefore('private');
        parent::initialize();
    }

    /**
     * Действие по умолчанию.
     */
    public function indexAction()
    {
    }

    public function show404Action(){
    }

}
