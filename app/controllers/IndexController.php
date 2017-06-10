<?php
namespace Mailer\Controllers;

/**
 * Отображение главной страницы.
 */
class IndexController extends ControllerBase
{

    /**
     * Установка шаблона (layouts/public.volt)
     */
    public function initialize() {
        $this->view->setTemplateBefore('public');
        parent::initialize();
    }

    /**
     * Действие по умолчанию.
     * Установка шаблона (layouts/public.volt)
     */
    public function indexAction()
    {
    }

    public function show404Action(){
    }

}
