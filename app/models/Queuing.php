<?php

namespace Mailer\Models;

use \Phalcon\Mvc\Model;

class Queuing extends Model
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
    protected $workerPid;

    /**
     *
     * @var integer
     */
    protected $jobId;

    /**
     *
     * @var integer
     */
    protected $userId;

    /**
     *
     * @var integer
     */
    protected $categoryId;

    /**
     *
     * @var integer
     */
    protected $messageId;

    /**
     *
     * @var integer
     */
    protected $groupId;

    /**
     *
     * @var string
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
    protected $lock;

    /**
     * Статус выполнения 0 - приостановлена, 1 - выполняется, 2 - выполнена
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
     * Method to set the value of field categoryId
     *
     * @param integer $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Method to set the value of field messageId
     *
     * @param integer $messageId
     * @return $this
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * Method to set the value of field groupId
     *
     * @param integer $groupId
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Method to set the value of field current
     *
     * @param integer $current
     * @return $this
     */
    public function setCurrent($current)
    {
        $this->current = $current;

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
     * Returns the value of field categoryId
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Returns the value of field messageId
     *
     * @return integer
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * Returns the value of field groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Returns the value of field current
     *
     * @return integer
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Returns the value of field totals
     *
     * @return integer
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Queuing
     */
    public function setStatus($status) {
        $this->status = $status;
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
     * @return Queuing
     */
    public function setWorkerPid($workerPid) {
        $this->workerPid = $workerPid;
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
     * @return Queuing
     */
    public function setJobId($jobId) {
        $this->jobId = $jobId;
        return $this;
    }

    /**
     * @return int
     */
    public function getLock() {
        return $this->lock;
    }

    /**
     * @param int $lock
     * @return Queuing
     */
    public function setLock($lock) {
        $this->lock = $lock;
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
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'queuing';
    }
	
    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        /**
         * Alters:
         * ALTER TABLE  `queuing` ADD  `workerPid` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `id` ,
        ADD  `jobId` INT( 64 )  NOT NULL DEFAULT  '0' AFTER  `workerPid` ;
         * ALTER TABLE  `queuing` ADD  `lock` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `totals` ;
         * ALTER TABLE  `queuing` CHANGE  `status`  `status` TINYINT( 1 ) NOT NULL COMMENT  ' 0 - приостановлена, 1 - выполняется, 2 - выполнена, 3 - ошибки';
         *
         *
         * ALTER TABLE  `queuing` CHANGE  `workerPid`  `workerPid` INT( 11 ) NOT NULL DEFAULT  '0';
         * ALTER TABLE  `queuing` CHANGE  `jobId`  `jobId` INT( 64 ) NOT NULL DEFAULT  '0';
         */

        return [
            'id' => 'id',
            'workerPid' => 'workerPid',
            'jobId' => 'jobId',
            'userId' => 'userId',
            'categoryId' => 'categoryId',
            'messageId' => 'messageId',
            'groupId' => 'groupId',
            'current' => 'current',
            'totals' => 'totals',
            'lock' => 'lock',
            'status' => 'status'
        ];
    }

    public function beforeValidationOnCreate(){

//        $this->workerPid = 0;
        $this->current = 0;
        $this->status = 0;
        $this->lock = 0;

    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
//        $this->workerPid = 0;
    }

    /**
     * Инциалзруем подстановку имён
     */
    public function initialize()
    {

        $this->belongsTo('userId', __NAMESPACE__ . '\Users', 'id', [
            'alias' => 'user'
        ]);

        $this->belongsTo('categoryId', __NAMESPACE__ . '\CategoryMessages', 'id', [
            'alias' => 'category'
        ]);

        $this->belongsTo('messageId', __NAMESPACE__ . '\MessagesTemplates', 'id', [
            'alias' => 'message'
        ]);

        $this->belongsTo('groupId', __NAMESPACE__ . '\EmailGroups', 'id', [
            'alias' => 'group'
        ]);
    }

}
