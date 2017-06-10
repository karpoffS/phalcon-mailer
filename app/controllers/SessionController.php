<?php
namespace Mailer\Controllers;

use Mailer\Auth\Auth;
use Mailer\Forms\LoginForm;
use Mailer\Forms\SignUpForm;
use Mailer\Forms\ForgotPasswordForm;
use Mailer\Auth\Exception as AuthException;
use Mailer\Models\InviteCode;
use Mailer\Models\Users;
use Mailer\Models\ResetPasswords;

/**
 * Контроллер авторизации сессии реализует такие действия как:
 * login/logout, регистрация пользователя, восстановления пароля
 */
class SessionController extends ControllerBase
{

    /**
     * Действие по умолчанию.
     * Установка шаблона (layouts/public.volt)
     */
    public function initialize()
    {
        $this->view->setTemplateBefore('public');
        parent::initialize();
    }

    public function indexAction()
    {
        $this->response->redirect("/signup");
    }

    /**
     * Регистрация пользователя в системе
     */
    public function signupAction()
    {
        $form = new SignUpForm();

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) != false) {

                $email = $this->filter->sanitize(trim($this->request->getPost('email')), 'email');

                // Получаем код приглашения
                $inviteCode = $this->request->getPost('invitationCode', 'striptags');

                /** @var InviteCode $invite */
                $invite = InviteCode::findFirst("code = '{$inviteCode}' AND status=0");

                if(!$invite){
                    $this->flash->error("Вы ввели не верный код приглашения");

                    return $this->dispatcher->forward([ 'action' => 'signup' ]);
                }

                if($invite->getEmail() !== $email){
                    $this->flash->error("Вы ввели не свой код приглашения");
                    return $this->dispatcher->forward([ 'action' => 'signup' ]);
                }

                /** @var Users $user */
                $user = new Users();

                $user->assign([
                    'name' => $this->request->getPost('name', 'striptags'),
                    'email' => $email,
                    'password' =>
                        $this->filter->sanitize(
                            trim($this->request->getPost('password')), 'string'
                        ),
                    'profilesId' => Auth::User
                ]);

                if ($user->save()) {

                    $invite->assign([
                        'userId' => $user->id,
                        'status' => 1
                    ]);

                    @$invite->update();

                    return $this->dispatcher->forward([
                        'controller' => 'index',
                        'action' => 'index'
                    ]);
                }

                $this->flash->error($user->getMessages());
            }
        }

        $this->view->form = $form;
    }

    /**
     * Запускает сеанса администратора в бэкэнде
     */
    public function loginAction()
    {
        $form = new LoginForm();

        /** @var \Mailer\Auth\Auth $auth */
        $auth = $this->getDI()->get('auth');

        try {

            if (!$this->request->isPost()) {

                if ($auth->hasRememberMe()) {
                    return $auth->loginWithRememberMe();
                }
            } else {

                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                } else {

                    $auth->check(array(
                        'email' => $this->request->getPost('email'),
                        'password' => $this->filter->sanitize(
                            trim($this->request->getPost('password')), 'string'
                        ),
                        'remember' => $this->request->getPost('remember')
                    ));

                    return $this->response->redirect('mailings');
                }
            }
        } catch (AuthException $e) {
            $this->flash->error($e->getMessage());
        }

        $this->view->form = $form;
    }

    /**
     * Показывает форму "забыли пароль"
     */
    public function forgotPasswordAction()
    {
        $form = new ForgotPasswordForm();

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                $user = Users::findFirstByEmail($this->request->getPost('email'));
                if (!$user) {
                    $this->flash->success('Нет пользователя связанного с этим электронным адресом');
                } else {

                    $resetPassword = new ResetPasswords();
                    $resetPassword->usersId = $user->id;
                    if ($resetPassword->save()) {
                        $this->flash->success('Пожалуйста, проверьте Ваши сообщения электронной почты для сброса пароля');
                    } else {
                        foreach ($resetPassword->getMessages() as $message) {
                            $this->flash->error($message);
                        }
                    }
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Закрывает сессию
     */
    public function logoutAction()
    {
        $this->auth->remove();

        return $this->response->redirect('/');
    }
}
