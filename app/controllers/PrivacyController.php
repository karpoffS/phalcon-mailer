<?php
namespace Mailer\Controllers;

/**
 * Вывести на экран страницу конфиденциальности.
 */
class PrivacyController extends ControllerBase
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
