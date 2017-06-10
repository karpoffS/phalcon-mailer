<?php
namespace Mailer\Controllers;

use Mailer\Acl\Acl;
use Phalcon\Config;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use PhalconRest\Http\Request;

/**
 * ControllerBase
 * Это базовый контроллов для всех контроллеров в приложении
 */
class ControllerBase extends Controller
{

    /**
     * Состояние в контроллере авторизованы ли мы
     * @var boolean
     */
    public $isAuth;

    /**
     * Инициализация
     */
    public function initialize() {

        // Авторизован?
        $this->isAuth = is_array($this->auth->getIdentity());

        $this->view->setVar('logged_in', $this->isAuth);
        $this->view->setVar('isPriveleged', $this->auth->isPriveleged());
    }

    /**
     * @param string $key
     * @param array $values
     * @return string
     */
    protected function getTranslation($key = null, $values = [])
    {
        return $this->getDI()->get("translate")->t($key, $values);
    }

    /**
     * Выполняется до вызова любого actions, чтобы мы могли определить,
     * если это частный контроллер, и должен пройти проверку подлинности,
     * или открытый контроллер, который открыт для всех.
     *
     * @param Dispatcher $dispatcher
     * @return boolean
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $controllerName = $dispatcher->getControllerName();

        /** @var Acl $acl */
        $acl = $this->acl;
        if ($acl->isPrivate($controllerName)) { // Проверка частных контроллеров

            // Получить текущий идентификатор
            $identity = $this->auth->getIdentity();

            // Если нет идентификатора доступа пользователь перенаправляется на главную
            if (!is_array($identity)) {

                $this->flash->notice($this->getTranslation('you-not-auth'));

                $dispatcher->forward(array(
                    'controller' => 'index',
                    'action' => 'index'
                ));
                return false;
            }

            // Проверка пользователя, имеет ли разрешение на текущий котроллер, действие
            $actionName = $dispatcher->getActionName();
            if (!$acl->isAllowed($identity['profile'], $controllerName, $actionName)) {

                $this->flash->notice('Вы не имеете доступа к этому разделу или действию!');

                if ($this->acl->isAllowed($identity['profile'], $controllerName, 'index')) {
                    $dispatcher->forward(array(
                        'controller' => $controllerName,
                        'action' => 'index'
                    ));
                } else {
                    $dispatcher->forward(array(
                        'controller' => 'user_control',
                        'action' => 'index'
                    ));
                }

                return false;
            }
        }
    }
}
