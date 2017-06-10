<?php
namespace Mailer\Models;

use Phalcon\Mvc\Model;

/**
 * FailedLogins
 * Эта модель регистрирует неудачные логины зарегистрированных и незарегистрированных пользователей
 */
class FailedLogins extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $usersId;

    /**
     *
     * @var string
     */
    public $ipAddress;

    /**
     *
     * @var integer
     */
    public $attempted;

    public function initialize()
    {
        $this->belongsTo('usersId', __NAMESPACE__ . '\Users', 'id', array(
            'alias' => 'user'
        ));
    }
}
