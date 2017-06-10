<?php
namespace Mailer\Controllers;

/**
 * Страница "О нас".
 */
class AboutController extends ControllerBase
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
     */
    public function indexAction()
    {
    }
}
