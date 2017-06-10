<?php
namespace Mailer\Controllers;

/**
 * Вывести на экран страницу с правилами и условиями.
 */
class TermsController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Действие по умолчанию.
     * Установка шаблона (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('public');
    }
}
