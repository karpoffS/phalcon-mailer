<?php
namespace Mailer\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Mailer\Models\Users
 * Класс работы с пользователями, зарегистрированных в приложении
 */
class Users extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $mustChangePassword;

    /**
     *
     * @var string
     */
    public $profilesId;

    /**
     *
     * @var string
     */
    public $banned;

    /**
     *
     * @var string
     */
    public $suspended;

    /**
     *
     * @var string
     */
    public $active;

    /**
     * Прежде чем создать пользователя, назначить пароль
     */
    public function beforeValidationOnCreate()
    {
        if (empty($this->password)) {

            // Создать простой временный пароль
            $tempPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

            // Пользователь должен изменить свой пароль при первом входе
            $this->mustChangePassword = 1;

            // Используйте этот пароль по умолчанию
            $this->password = $tempPassword;
//            $this->password = $this->getDI()
//                ->getSecurity()
//                ->hash($tempPassword);


        } else {
            // Пользователь не должен изменить свой пароль при первом входе
            $this->mustChangePassword = 0;
        }

        // Учетная запись должна быть подтверждена по электронной почте
        $this->active = 0;

        // Учетная запись не приостанавливается по умолчанию
        $this->suspended = 0;

        // Аккаунт не забанен по умолчанию
        $this->banned = 0;
    }

    /**
     * Отправить подтверждение по электронной почте пользователю, если учетная запись не активна
     */
    public function afterSave()
    {

        if ($this->active == 0) {

            $emailConfirmation = new EmailConfirmations();

            $emailConfirmation->usersId = $this->id;

            if ($emailConfirmation->save()) {
                $this->getDI()
                    ->get('flash')
                    ->notice('На ваш почтовый ящик '. $this->email .' отправлено сообщение. <br/> Для активации учётной записи, перейдите по ссылке!');
            }
        }

        // Создаём группу автоматически
        $emailGroup = new EmailGroups();
        $emailGroup
            ->setUserId($this->id)
            ->setName('Default')
            ->setDescription("List by default")
            ->setStatus(1);

        // Сохранияем
        $emailGroup->save();


    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return [
            'id' => 'id',
            'name' => 'name',
            'email' => 'email',
            'password' => 'password',
            'mustChangePassword' => 'mustChangePassword',
            'profilesId' => 'profilesId',
            'banned' => 'banned',
            'suspended' => 'suspended',
            'active' => 'active'
        ];
    }

    /**
     * Проверка на уникальность emails для различных пользователей.
     */
    public function validation()
    {
        $validation = new Validation();

        $validation
            ->add('email', new Uniqueness([
                'model'   => $this,
                "message" => "Этот адресс электронной почты уже зарегистрирован"
            ]));

        return $this->validate($validation);
    }

    public function initialize()
    {
        $this->belongsTo('profilesId', __NAMESPACE__ . '\Profiles', 'id', [
            'alias' => 'profile',
            'reusable' => true
        ]);

        $this->hasMany('id', __NAMESPACE__ . '\SuccessLogins', 'usersId', [
            'alias' => 'successLogins',
            'foreignKey' => [
                'message' => 'Пользователь не может быть удален, потому что он/она обладает активностью в системе'
            ]
        ]);

        $this->hasMany('id', __NAMESPACE__ . '\PasswordChanges', 'usersId', [
            'alias' => 'passwordChanges',
            'foreignKey' => [
                'message' => 'Пользователь не может быть удален, потому что он/она обладает активностью в системе'
            ]
        ]);

        $this->hasMany('id', __NAMESPACE__ . '\ResetPasswords', 'usersId', [
            'alias' => 'resetPasswords',
            'foreignKey' => [
                'message' => 'Пользователь не может быть удален, потому что он/она обладает активностью в системе'
            ]
        ]);
    }
}
