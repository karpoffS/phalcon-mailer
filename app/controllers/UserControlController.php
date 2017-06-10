<?php
namespace Mailer\Controllers;

use Mailer\Models\EmailConfirmations;
use Mailer\Models\ResetPasswords;

/**
 * UserControlController
 * Provides help to users to confirm their passwords or reset them
 * Оказывает помощь пользователям, чтобы подтвердить свои пароли или сбросить их
 */
class UserControlController extends ControllerBase
{

    public function initialize()
    {
        $this->view->setTemplateBefore('private');
        parent::initialize();
    }


    public function indexAction()
    {

    }

    /**
     * Подтверждение e-mail.
     * Если пользователь хочет изменить свой пароль, то изменяет его
     */
    public function confirmEmailAction()
    {
        $code = $this->dispatcher->getParam('code');

        $confirmation = EmailConfirmations::findFirstByCode($code);

        if (!$confirmation) {
            return $this->dispatcher->forward(array(
                'controller' => 'index',
                'action' => 'index'
            ));
        }

        if ($confirmation->confirmed != 0) {
            return $this->dispatcher->forward(array(
                'controller' => 'session',
                'action' => 'login'
            ));
        }

        $confirmation->confirmed = 1;

        $confirmation->user->active = 1;

        /**
         * Изменяет поле подтверждения 'confirmed' и обновияет статус пользователя на "active"
         */
        if (!$confirmation->save()) {

            foreach ($confirmation->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                'controller' => 'index',
                'action' => 'index'
            ));
        }

        /**
         * Идентификация пользователя в приложении
         */
        $this->getDI()->get('auth')->authUserById($confirmation->user->id);

        /**
         * Проверить, должен ли пользователь изменить свой пароль
         */
        if ($confirmation->user->mustChangePassword == 1) {

            $this->flash->success('Электронная почта была успешно подтверждена. Теперь вы должны изменить свой пароль');

            return $this->dispatcher->forward([
                'controller' => 'users',
                'action' => 'changePassword'
            ]);
        }

        $this->flash->success('Электронная почта была успешно подтверждена!');

        return $this->response->redirect('mailings');
//
//        return $this->dispatcher->forward([
//            'controller' => 'mailings',
//            'action' => 'index'
//        ]);
    }

    /**
     * Сброс пароля
     */
    public function resetPasswordAction()
    {
        $code = $this->dispatcher->getParam('code');

        $resetPassword = ResetPasswords::findFirstByCode($code);

        if (!$resetPassword) {
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }

        if ($resetPassword->reset != 0) {
            return $this->dispatcher->forward([
                'controller' => 'session',
                'action' => 'login'
            ]);
        }

        $resetPassword->reset = 1;

        /**
         * Изменение подтверждения сброса 'reset'
         */
        if (!$resetPassword->save()) {

            foreach ($resetPassword->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }

        /**
         * Идентификация пользователя в приложении
         */
        $this->getDI()->get('auth')->authUserById($resetPassword->usersId);

        $this->flash->success('Пожалуйста, сбросьте свой пароль!');

        return $this->dispatcher->forward([
            'controller' => 'users',
            'action' => 'changePassword'
        ]);
    }
}
