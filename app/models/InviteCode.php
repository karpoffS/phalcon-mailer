<?php

namespace Mailer\Models;

use \Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

class InviteCode extends Model
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
    protected $email;

    /**
     *
     * @var string
     */
    protected $code;

    /**
     *
     * @var integer
     */
    protected $createAt;

    /**
     *
     * @var integer
     */
    protected $modifyAt;

    /**
     *
     * @var integer
     */
    protected $status;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field userId
     *
     * @param integer $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param int $createAt
     * @return InviteCode
     */
    public function setCreateAt($createAt) {
        $this->createAt = $createAt;
        return $this;
    }

    /**
     * @param int $modifyAt
     * @return InviteCode
     */
    public function setModifyAt($modifyAt) {
        $this->modifyAt = $modifyAt;
        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param integer $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getCreateAt() {
        return $this->createAt;
    }

    /**
     * @return int
     */
    public function getModifyAt() {
        return $this->modifyAt;
    }

    /**
     * Returns the value of field status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function beforeValidationOnCreate(){

        $tempCode = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(8)));

        $this->status = 0;

        $this->createAt = time();

        $this->modifyAt = 0;

        $this->code = $tempCode;
    }

    public function beforeValidationOnUpdate()
    {
        // Timestamp on the update
        $this->modifyAt = time();
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {

        $validation = new Validation();

        $validation->add('email',
            new PresenceOf([
                'model'   => $this,
                'message' => 'Это поле должно быть заполнено',
                'required' => true
            ])
        );

        $validation->add('email', new Email(['model'   => $this, 'required' => true ]));

        $validation->add("email",
            new Uniqueness([
                'model'   => $this,
                "message" => "Этот адресс электронной почты уже имеется в списке приглашоных"
            ])
        );

        return $this->validate($validation);
    }

    /**
     * @return Model\Resultset|\Phalcon\Mvc\Phalcon\Mvc\Model|string
     */
    public function getName() {
        return isset($this->name) ? $this->name : " ";
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'invite_code';
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
            'email' => 'email',
            'code' => 'code',
            'createAt' => 'createAt',
            'modifyAt' => 'modifyAt',
            'status' => 'status'
        );
    }

    /**
     * Инциалзруем подстановку имён
     */
    public function initialize()
    {

        $this->belongsTo('userId', __NAMESPACE__ . '\Users', 'id', [
            'alias' => 'user'
        ]);
    }

}
