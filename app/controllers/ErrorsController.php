<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 22.07.16
 * Time: 22:26
 */

namespace Mailer\Controllers;

use Phalcon\Mvc\View;

class ErrorsController extends ControllerBase {

    public function initialize(){

        $this->response->setStatusCode(404,'Not_Found');
        $this->view->setTemplateBefore('public');

        parent::initialize();
    }

    public function show404Action(){

    }
}
