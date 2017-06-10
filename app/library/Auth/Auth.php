<?php
namespace Mailer\Auth;

use Phalcon\Mvc\User\Component;
use Mailer\Models\Users;
use Mailer\Models\RememberTokens;
use Mailer\Models\SuccessLogins;
use Mailer\Models\FailedLogins;

/**
 * Mailer\Auth\Auth
 * Управляет Аутентификацией/Идетификацией в приложении
 */
class Auth extends Component
{

    /**
     * Админы
     */
    const Administrator = 2;

    /**
     * Модераторы
     */
    const Moderator = 3;

    /**
     * Пользователи
     */
    const User = 4;

    /**
     * Привелигированные
     */
    const Priveleged = [ 1, 2, 3 ];

    /**
     * Проверяет учетные данные пользователя
     *
     * @param array $credentials
     * @return boolean
     * @throws Exception
     */
    public function check($credentials)
    {

        // существует ли пользователь
        $user = Users::findFirstByEmail($credentials['email']);
        if ($user == false) {
            $this->registerUserThrottling(0);
            throw new Exception('Неверный email');
        }

        // Проверка пароля
//        if (!$this->security->checkHash($credentials['password'], $user->password)) {
        if ($credentials['password'] !== $user->password) {
            $this->registerUserThrottling($user->id);
            throw new Exception('Неверный пароль');
        }

        // Проверка флагов
        $this->checkUserFlags($user);

        // Регистрация успешной авторизации
        $this->saveSuccessLogin($user);

        // Проверка, установлена ли галочка "remember me"
        if (isset($credentials['remember'])) {
            $this->createRememberEnvironment($user);
        }

        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'profile' => $user->profile->name
        ));
    }

    /**
     * Создает "Запомнить меня" параметры среды, связывает cookies(печенки) и генерирующие tokens
     *
     * @param \Mailer\Models\Users $user
     * @throws Exception
     */
    public function saveSuccessLogin($user)
    {
        $successLogin = new SuccessLogins();
        $successLogin->usersId = $user->id;
        $successLogin->ipAddress = $this->request->getClientAddress();
        $successLogin->userAgent = $this->request->getUserAgent();
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new Exception($messages[0]);
        }
    }

    /**
     *
     * Реализует прослойку(дросель) авторизации
     * Снижает эффективность атаки типа "brute force" (Перебора паролей)
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->usersId = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->save();

        $attempts = FailedLogins::count([
            'ipAddress = ?0 AND attempted >= ?1',
            'bind' => [
                $this->request->getClientAddress(),
                time() - 3600 * 6
            ]
        ]);

        // При большем количестве попыток, дольше ожидают
        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
                sleep(2);
                break;
            case 4:
                sleep(5);
                break;
            case 5:
                sleep(7);
                break;
            case 6:
                sleep(10);
                break;
            default:
                sleep(15);
                break;
        }
    }

    /**
     * Создает "Запомнить меня" параметры среды, связывает cookies(печенки) и генерирует tokens
     *
     * @param \Mailer\Models\Users $user
     */
    public function createRememberEnvironment(Users $user)
    {
        $userAgent = $this->request->getUserAgent();
        $token = md5($user->email . $user->password . $userAgent);

        $remember = new RememberTokens();
        $remember->usersId = $user->id;
        $remember->token = $token;
        $remember->userAgent = $userAgent;

        if ($remember->save() != false) {
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, $expire);
            $this->cookies->set('RMT', $token, $expire);
        }
    }

    /**
     * Проверка на чекбокс "Помнить меня"
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }

    /**
     * Logs по использованию информации в cookies(печенье)
     *
     * @return \Phalcon\Http\Response
     */
    public function loginWithRememberMe()
    {
        $userId = $this->cookies->get('RMU')->getValue();
        $cookieToken = $this->cookies->get('RMT')->getValue();

        $user = Users::findFirstById($userId);
        if ($user) {

            $userAgent = $this->request->getUserAgent();
            $token = md5($user->email . $user->password . $userAgent);

            if ($cookieToken == $token) {

                $remember = RememberTokens::findFirst(array(
                    'usersId = ?0 AND token = ?1',
                    'bind' => array(
                        $user->id,
                        $token
                    )
                ));
                if ($remember) {

                    // Проверка cookies на вышедщий срок
                    if ((time() - (86400 * 8)) < $remember->createdAt) {

                        // Проверяем статусы флагов
                        $this->checkUserFlags($user);

                        // Сохраняем идентификацию
                        $this->session->set('auth-identity', array(
                            'id' => $user->id,
                            'name' => $user->name,
                            'profile' => $user->profile->name
                        ));

                        // Сохраняем сессию и логинимся
                        $this->saveSuccessLogin($user);

                        return $this->response->redirect('profiles');
                    }
                }
            }
        }

        $this->cookies->get('RMU')->delete();
        $this->cookies->get('RMT')->delete();

        return $this->response->redirect('/login');
    }

    /**
     * Проверяет, является ли пользователь banned(запрещен)/inactive(неактивный)/suspended(приостановлено)
     *
     * @param \Mailer\Models\Users $user
     * @throws Exception
     */
    public function checkUserFlags(Users $user)
    {
        if ($user->active != 1) {
            throw new Exception('Вы не активировали свою учётную запись!');
        }

        if ($user->banned != 0) {
            throw new Exception('Пользователь заблокирован');
        }

        if ($user->suspended != 0) {
            throw new Exception('Пользователь в отпуске');
        }
    }

    /**
     * Получить текущую идентификацию
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->session->get('auth-identity');
    }

    /**
     * Получить имя текущей идентификации
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['name'];
    }

    /**
     * Удаляет информацию о личности пользователя из сеанса
     */
    public function remove()
    {
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }

        $this->session->remove('auth-identity');
    }

    /**
     * Авторизация пользователя по ID
     *
     * @param int $id
     * @throws Exception
     */
    public function authUserById($id)
    {
        $user = Users::findFirstById($id);
        if ($user == false) {
            throw new Exception('Пользователь не существует');
        }

        $this->checkUserFlags($user);

        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'profile' => $user->profile->name
        ));
    }

    /**
     * Получить объект, связанный с пользователем в активной идентичности
     *
     * @return \Mailer\Models\Users
     * @throws Exception
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id'])) {

            $user = Users::findFirstById($identity['id']);
            if ($user == false) {
                throw new Exception('Пользователь не существует');
            }

            return $user;
        }

        return false;
    }

    /**
     * Проверка на админа
     * @return bool
     * @throws Exception
     */
    public function isPriveleged() {
        if(!$this->session->get('auth-identity')){
            return false;
        }
        return in_array($this->getUser()->profilesId, self::Priveleged);
    }
}
