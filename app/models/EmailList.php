<?php

namespace Mailer\Models;

use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation;
use Phalcon\Mvc\Model;


/**
 * Class EmailList
 *
 * @package Mailer\Models
 */
class EmailList extends Model
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
     * @var integer
     */
    protected $groupId;

    /**
     *
     * @var string
     */
    protected $address;

    /**
     *
     * @var string
     */
    protected $name;
    
    /**
     *
     * @var integer
     */
    protected $confirmed;

    /**
     *
     * @var integer
     */
    protected $unsubscribe;

    /**
     *
     * @var integer
     */
    protected $status;

//    public function __set($property, $value) {
//        if (is_array($value) || is_object($value)) {
//            return parent::__set($property, $value);
//        }
//        if ($this->_possibleSetter($property, $value)) {
//            return $value();
//        }
//        $this->{$property} = $value;
//        return $value();
//    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EmailList
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
     * @return EmailList
     */
    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     * @return EmailList
     */
    public function setGroupId($groupId) {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param string $address
     * @return EmailList
     */
    public function setAddress($address) {
        $this->address = $address;
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
     * @return EmailList
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getConfirmed() {
        return $this->confirmed;
    }

    /**
     * @param int $confirmed
     * @return EmailList
     */
    public function setConfirmed($confirmed) {
        $this->confirmed = $confirmed;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnsubscribe() {
        return $this->unsubscribe;
    }

    /**
     * @param int $unsubscribe
     * @return EmailList
     */
    public function setUnsubscribe($unsubscribe) {
        $this->unsubscribe = $unsubscribe;
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
     * @return EmailList
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {

        $validation = new Validation();

        $validation
            ->add('address', new Email([
                'required' => true
            ]))
            ->add([ 'address', 'groupId' ], new Uniqueness([
                'model'   => $this,
                'message' => "Адресс {$this->address} электронной почты уже имеется в списке - {$this->group->name}!"
            ]));

        return $this->validate($validation);
//
//        $this->validate(new Uniqueness([
//            "field" => [ 'address', 'groupId' ],
//            "message" => "Адресс {$this->address} электронной почты уже имеется в списке - {$this->group->name}!"
//        ]));
//
//        if ($this->validationHasFailed() == true) {
//            return false;
//        }
//
//        return true;

    }

    public function beforeValidationOnCreate()
    {
        $this->unsubscribe = 0;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'email_list';
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
            'groupId' => 'groupId',
            'address' => 'address',
            'name' => 'name',
            'confirmed' => 'confirmed',
            'unsubscribe' => 'unsubscribe',
            'status' => 'status'
        );
    }

    public function initialize()
    {
        $this->belongsTo("userId", __NAMESPACE__ .'\Users', "id", [
            'alias' => 'user'
        ]);

        $this->belongsTo("groupId", __NAMESPACE__ .'\EmailGroups', "id", [
            'alias' => 'group'
        ]);
    }
}
