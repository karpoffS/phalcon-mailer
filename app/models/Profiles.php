<?php
namespace Mailer\Models;

use Phalcon\Mvc\Model;

/**
 * Mailer\Models\Profiles
 * Все уровни профиля в приложении. Используется в сочетании со списками ACL
 */
class Profiles extends Model
{

    /**
     * ID
     * @var integer
     */
    public $id;

    /**
     * Name
     * @var string
     */
    public $name;

    /**
     * Определение отношений к пользователям и их разрешений
     */
    public function initialize()
    {
        $this->hasMany('id', __NAMESPACE__ . '\Users', 'profilesId', [
            'alias' => 'users',
            'foreignKey' => [
                'message' => 'Профиль не может быть удален, потому что используется у пользователей'
            ]
        ]);

        $this->hasMany('id', __NAMESPACE__ . '\Permissions', 'profilesId', [
            'alias' => 'permissions'
        ]);
    }
}
