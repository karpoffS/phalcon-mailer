<?php

namespace Mailer\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
//use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class EmailGroups
 *
 * @package Mailer\Models
 */
class EmailGroups extends Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var integer
     */
    protected $userId;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description;

    /**
     *
     * @var integer
     */
    protected $isDefault;

    /**
     *
     * @var integer
     */
    protected $status;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EmailGroups
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return EmailGroups
     */
    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EmailGroups
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     * @return EmailGroups
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param int $status
     * @return EmailGroups
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'userId' => 'userId',
            'name' => 'name',
            'description' => 'description',
            'isDefault' => 'isDefault',
            'status' => 'status'
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'email_groups';
    }

    /**
     * Проверка на уникальность названия группы
     *
     * @return bool
     */
    public function validation() {

        $validation = new Validation();

        $validation
            ->add(['userId', 'name'], new Uniqueness([
                'model'   => $this,
                "message" => "Такая группа уже имеется!"
            ]));

        return $this->validate($validation);
    }
}
