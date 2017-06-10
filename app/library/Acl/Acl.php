<?php
namespace Mailer\Acl;

use Phalcon\Mvc\User\Component;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;
use Mailer\Models\Profiles;

/**
 * Mailer\Acl\Acl
 */
class Acl extends Component
{

    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * Путь к фалу кеша ACL из APP_PATH
     *
     * @var string
     */
    private $filePath = '/app/cache/acl/data.txt';

    /**
     * Определяет ресурсы, которые считются "приватними". Контроллеры требующе авторизации.
     *
     * @var array
     */
    private $privateResources = [
        'users' => [
            'index',
            'search',
            'edit',
            'create',
            'delete',
            'changePassword'
        ],
        'profiles' => [
            'index',
            'search',
            'edit',
            'create',
            'delete'
        ],
        'permissions' => [
            'index'
        ],
        'queuing' => [
            'index',
            'create',
            'delete',
            'play',
            'list'
        ],
        'groups' => [
            'index',
            'create',
            'edit',
            'delete'
        ],
        'importings' => [
            'index',
            'create',
            'delete',
            'pause',
            'parse'
        ],
        'mailings' => [
            'index',
            'command',
            'delete'
        ],
        'messages' => [
            'index',
            'create',
            'edit',
            'delete'
        ],
        'categories' => [
            'index',
            'create',
            'edit',
            'delete'
        ],
        'profile' => [
            'index',
        ],
        'todo' => [
            'index',
        ],
        'invitings' => [
            'index',
            'create',
            'mailing',
            'delete',
        ],
    ];

    /**
     * Описания действий, используемых в {@see $privateResources}
     *
     * @var array
     */
    private $actionDescriptions = array(
        'command' => 'Status change',
        'mailing' => 'Sending message',
        'play' => 'Start action',
        'pause' => 'Stop action',
        'parse' => 'Runing parse action',
        'list' => 'Listing',
        'index' => 'Access',
        'search' => 'Search',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'changePassword' => 'Change password'
    );

    /**
     * Проверяет, является ли контроллер "приватным" или нет
     *
     * @param string $controllerName
     * @return boolean
     */
    public function isPrivate($controllerName)
    {
        $controllerName = strtolower($controllerName);
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Проверяет, разрешается ли текущему профилю доступ к ресурсу
     *
     * @param string $profile
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($profile, $controller, $action)
    {
        return $this->getAcl()->isAllowed($profile, $controller, $action);
    }

    /**
     * Возвращает список ACL
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }

        // Check if the ACL is in APC
        if (function_exists('apc_fetch')) {
            $acl = apc_fetch('Mailer-acl');
            if (is_object($acl)) {
                $this->acl = $acl;
                return $acl;
            }
        }

        // Check if the ACL is already generated
        if (!file_exists(APP_PATH . $this->filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Get the ACL from the data file
        $data = file_get_contents(APP_PATH . $this->filePath);
        $this->acl = unserialize($data);

        // Store the ACL in APC
        if (function_exists('apc_store')) {
            apc_store('Mailer-acl', $this->acl);
        }

        return $this->acl;
    }

    /**
     * Возвращает разрешения, назначенные профилю
     *
     * @param Profiles $profile
     * @return array
     */
    public function getPermissions(Profiles $profile)
    {
        $permissions = array();
        foreach ($profile->getPermissions() as $permission) {
            $permissions[$permission->resource . '.' . $permission->action] = true;
        }
        return $permissions;
    }

    /**
     * Возвращает все ресурсы и их действия, доступные в приложении
     *
     * @return array
     */
    public function getResources()
    {
        return $this->privateResources;
    }

    /**
     * Возвращает описание действий в соответствии с его упрощенным наименованием
     *
     * @param string $action
     * @return string
     */
    public function getActionDescription($action)
    {
        if (isset($this->actionDescriptions[$action])) {
            return $this->actionDescriptions[$action];
        } else {
            return $action;
        }
    }

    /**
     * Перестраивает список доступа в файл
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function rebuild()
    {
        $acl = new AclMemory();

        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        // Пролучаем роли
        $profiles = Profiles::find('active = 1 AND hiding = 0');

        foreach ($profiles as $profile) {
            $acl->addRole(new AclRole($profile->name));
        }

        foreach ($this->privateResources as $resource => $actions) {
            $acl->addResource(new AclResource($resource), $actions);
        }

        // Предоставление доступа к private зоне роли Users
        foreach ($profiles as $profile) {

            // Предоставьте разрешения в модели "permissions"
            foreach ($profile->getPermissions() as $permission) {
                $acl->allow($profile->name, $permission->resource, $permission->action);
            }

            // Всегда разрешаем смену пароля
            $acl->allow($profile->name, 'users', 'changePassword');
        }

        if (touch(APP_PATH . $this->filePath) && is_writable(APP_PATH . $this->filePath)) {

            file_put_contents(APP_PATH . $this->filePath, serialize($acl));

            // Сохраняем ACL в APC
            if (function_exists('apc_store')) {
                apc_store('Mailer-acl', $acl);
            }
        } else {
            $this->flash->error(
                'Нет прав доступа на запись файла списока ACL: ' . APP_PATH . $this->filePath
            );
        }

        return $acl;
    }
}
