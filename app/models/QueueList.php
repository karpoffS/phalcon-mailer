<?php

namespace Mailer\Models;

use \Phalcon\Mvc\Model;

class QueueList extends Model
{

    /**
     * Номер записи, autoincrement
     *
     * @var integer
     */
    protected $id;

    /**
     * Номер задачи в Beanstalk
     *
     * @var integer
     */
    protected $jobId;

    /**
     * Pid процесса worker-а
     *
     * @var integer
     */
    protected $workerPid;

    /**
     * Id Сформированой очереди
     *
     * @var integer
     */
    protected $queueId;

    /**
     *  Владелец очереди
     *
     * @var integer
     */
    protected $userId;

    /**
     * Id Шаблона
     *
     * @var integer
     */
    protected $messageId;

    /**
     * Категоря шаблона
     *
     * @var integer
     */
    protected $categoryId;

    /**
     * Группа рассылки
     *
     * @var integer
     */
    protected $groupId;

    /**
     * Конкретный email адрес рассылки
     *
     * @var integer
     */
    protected $emailId;

    /**
     * Время создания задачи
     *
     * @var integer
     */
    protected $createAt;

    /**
     * Время выполнения задачи
     *
     * @var integer
     */
    protected $modifyAt;

    /**
     * Кол-во попыток выполнения задачи
     *
     * @var integer
     */
    protected $attempts;

    /**
     * Ошибки отправки
     *
     * @var integer
     */
    protected $errors;

    /**
     * Блокирование задачи на выполнение: 0 - свободно, 1 - заблокирован
     *
     * @var integer
     */
    protected $lock;

    /**
     * Состояние задачи: 0 - в очереди, 1 - отправлено, 2 - снято с очереди
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
     * Method to set the value of field jobId
     *
     * @param integer $jobId
     * @return $this
     */
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;

        return $this;
    }

    /**
     * @param int $workerPid
     * @return QueueList
     */
    public function setWorkerPid($workerPid) {
        $this->workerPid = $workerPid;
        return $this;
    }

    /**
     * @param integer $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
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
     *
     * @param integer $emailId
     * @return $this
     */
    public function setEmailId($emailId)
    {
        $this->emailId = $emailId;

        return $this;
    }

    /**
     *
     * @param integer $createAt
     * @return $this
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     *
     * @param integer $modifyAt
     * @return $this
     */
    public function setModifyAt($modifyAt)
    {
        $this->modifyAt = $modifyAt;

        return $this;
    }

    /**
     *
     * @param integer $attempts
     * @return $this
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * @param int $errors
     * @return QueueList
     */
    public function setErrors($errors) {
        $this->errors = $errors;
        return $this;
    }

    /**
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        // 0 - в очереди, 1 - отправлено, 2 - снято с очереди
        if($status == 'queue'){
            $this->status = 0;
        }

        if($status == 'sends') {
            $this->status = 1;
        }

        if($status == 'cancel') {
            $this->status = 2;
        }


        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return integer
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * @return int
     */
    public function getWorkerPid() {
        return $this->workerPid;
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @return integer
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     *
     * @return integer
     */
    public function getEmailId()
    {
        return $this->emailId;
    }

    /**
     *
     * @return integer
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     *
     * @return integer
     */
    public function getModifyAt()
    {
        return $this->modifyAt;
    }

    /**
     *
     * @return integer
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * @return int
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
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
    public function getQueueId() {
        return $this->queueId;
    }

    /**
     * @param int $queueId
     * @return QueueList
     */
    public function setQueueId($queueId) {
        $this->queueId = $queueId;
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
     * @return QueueList
     */
    public function setLock($lock) {
        $this->lock = $lock;
        return $this;
    }


    public function beforeValidationOnCreate() {

        $this->workerPid = 0;
        $this->createAt = time();
        $this->modifyAt = 0;
        $this->attempts = 0;
        $this->errors = null;
        $this->lock = 0;
        $this->status = 0;
    }


    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        // Timestamp on the update
        $this->modifyAt = time();
    }


    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'queue_list';
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
            'queueId' => 'queueId',
            'userId' => 'userId',
            'messageId' => 'messageId',
            'categoryId' => 'categoryId',
            'groupId' => 'groupId',
            'emailId' => 'emailId',
            'createAt' => 'createAt',
            'modifyAt' => 'modifyAt',
            'attempts' => 'attempts',
            'errors' => 'errors',
            'lock' => 'lock',
            'status' => 'status'
        ];
    }


    public function initialize()
    {
        $this->belongsTo('userId', __NAMESPACE__ . '\Users', 'id', [
            'alias' => 'user'
        ]);

        $this->belongsTo('groupId', __NAMESPACE__ . '\EmailGroups', 'id', [
            'alias' => 'group'
        ]);

        $this->belongsTo('categoryId', __NAMESPACE__ . '\CategoryMessages', 'id', [
            'alias' => 'category'
        ]);

        $this->belongsTo('messageId', __NAMESPACE__ . '\MessagesTemplates', 'id', [
            'alias' => 'message'
        ]);

        $this->belongsTo('emailId', __NAMESPACE__ . '\EmailList', 'id', [
            'alias' => 'email'
        ]);
    }

}
