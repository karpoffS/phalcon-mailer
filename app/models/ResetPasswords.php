<?php
namespace Mailer\Models;

use Phalcon\Mvc\Model;
use Mailer\Mailings\Mailer;
use Phalcon\Queue\Beanstalk;

/**
 * ResetPasswords
 * Stores the reset password codes and their evolution
 */
class ResetPasswords extends Model
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
    public $code;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $modifiedAt;

    /**
     *
     * @var string
     */
    public $reset;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->createdAt = time();

        // Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Set status to non-confirmed
        $this->reset = 0;
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        // Timestamp the confirmaton
        $this->modifiedAt = time();
    }

    /**
     * Send an e-mail to users allowing him/her to reset his/her password
     */
    public function afterCreate()
    {
        /** @var Mailer $mailer */
        $mailer = $this->getDI()->get('mailer');

        $mailer->queue('emailTemplates/reset', [
            // Переменные для передачи в шаблон
            'publicUrl' => $this->getDI()->get('config')->application->publicUrl,
            'resetUrl' => '/reset-password/' . $this->code . '/' . $this->user->email
        ], [
            // Переменные для передачи в
            'address' => [ $this->user->email => $this->user->name ],
            'subject' => "Восстановление пароля"
        ]);

    }

    public function initialize()
    {
        $this->belongsTo('usersId', __NAMESPACE__ . '\Users', 'id', array(
            'alias' => 'user'
        ));
    }
}
