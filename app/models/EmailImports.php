<?php

namespace Mailer\Models;

use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;


/**
 * EmailImportQueue
 * 
 * @autogenerated by Phalcon Developer Tools
 * @date 2016-08-03, 00:40:35
 */
class EmailImports extends Model
{

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false)
     */
    protected $id;

    /**
     * @Column(type="integer", nullable=true)
     * @var integer
     */
    protected $jobId;

    /**
     * @Column(type="integer", nullable=true)
     * @var integer
     */
    protected $workerPid;

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
    protected $filename;

    /**
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var integer
     */
    protected $size;

    /**
     *
     * @var integer
     */
    protected $current;

    /**
     *
     * @var integer
     */
    protected $totals;

    /**
     *
     * @var integer
     */
    protected $checking;

    /**
     * @Column(type="integer", nullable=false)
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
     * Method to set the value of field userid
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
     * @param int $groupId
     * @return EmailImports
     */
    public function setGroupId($groupId) {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Method to set the value of field filename
     *
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Method to set the value of field totals
     *
     * @param integer $totals
     * @return $this
     */
    public function setTotals($totals)
    {
        $this->totals = $totals;

        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status = "added")
    {

        // 0 - В обработке, 1 - Обработан, 2 - В очереди, 3 - Отменён
        switch($status){
            case "done":
                $status = 1;
                break;

            case "queue":
                $status = 2;
                break;

            case "cancel":
                $status = 3;
                break;

            case "added":
            default: $status = 0; break;
        }

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
     * Returns the value of field userid
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * Returns the value of field filename
     *
     * @return string
     */
    public function getFilename($hash = false)
    {
        if($hash){
            $result = explode(".", $this->filename);
            return $result[0];
        }

        return $this->filename;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns the value of field totals
     *
     * @return integer
     */
    public function getTotals($format = false, $decimals = 0 , $dec_point = '.' , $thousands_sep = ',')
    {
        return $format ? number_format($this->totals, $decimals, $dec_point, $thousands_sep) : $this->totals ;
    }

    /**
     * @return int
     */
    public function getChecking() {
        return $this->checking;
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

    /**
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getJobId() {
        return $this->jobId;
    }

    /**
     * @param int $jobId
     * @return EmailImports
     */
    public function setJobId($jobId) {
        $this->jobId = $jobId;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkerPid() {
        return $this->workerPid;
    }

    /**
     * @param int $workerPid
     * @return EmailImports
     */
    public function setWorkerPid($workerPid) {
        $this->workerPid = $workerPid;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrent() {
        return $this->current;
    }

    /**
     * @param int $current
     * @return EmailImports
     */
    public function setCurrent($current) {
        $this->current = $current;
        return $this;
    }

    /**
     * @param int $checking
     * @return EmailImports
     */
    public function setChecking($checking) {
        $this->checking = $checking;
        return $this;
    }

    /**
     * @param bool $decimal
     * @return float
     */
    public function getPercent($decimal = true){

        $percent = (100 * $this->getCurrent()) / $this->getTotals();

        $calc = $decimal ? number_format($percent, 2) : number_format($percent, 0);

        $result = ceil($calc) >= 100 ? 100 : $calc;

        return $result;
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
            'jobId' => 'jobId',
            'workerPid' => 'workerPid',
            'userId' => 'userId',
            'groupId' => 'groupId',
            'filename' => 'filename',
            'type' => 'type',
            'size' => 'size',
            'current' => 'current',
            'totals' => 'totals',
            'checking' => 'checking',
            'status' => 'status'
        ];
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'email_imports';
    }

    public function initialize()
    {
        $this->belongsTo('userId', __NAMESPACE__ . '\Users', 'id', [
            'alias' => 'users'
        ]);

        $this->belongsTo('groupId', __NAMESPACE__ . '\EmailGroups', 'id', [
            'alias' => 'group'
        ]);
    }

    /**
     * Задаём параметры по умолчанию
     */
    public function beforeValidationOnCreate()
    {
        $this->jobId = 0;
        $this->workerPid = 0;
//        $this->current = 0;
        $this->checking = 0;
        $this->status = 2;
    }

    public function validation()
    {
        $validation = new Validation();

        // Смотрим на уникальность имени файла у пользователя
        $unique = new Uniqueness([
            'model'   => $this,
            "message" => "Этот список вы уже загружали в группу <b>{$this->group->getName()}</b>!"
        ]);

        $validation->add([ 'userId', 'groupId', 'filename' ], $unique);

        return $this->validate($validation);
    }

    /**
     * Вывод сообщений
     *
     * @param null $flash
     */
    public function notSaveMessages($flash = null)
    {
        // Obtain the flash service from the DI container
        $flash = $flash ? $flash : $this->getDI()->getShared('flash');

        // Show validation messages
        foreach ($this->getMessages() as $message) {
            $flash->error((string) $message);
        }
    }

}
