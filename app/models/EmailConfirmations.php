<?php
namespace Mailer\Models;

use Mailer\Mailer;
use Phalcon\Mvc\Model;

/**
 * EmailConfirmations
 * Stores the reset password codes and their evolution
 */
class EmailConfirmations extends Model
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

    public $confirmed;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmation
        $this->createdAt = time();

        // Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Set status to non-confirmed
        $this->confirmed = 0;
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        // Timestamp the confirmation
        $this->modifiedAt = time();
    }

    /**
     * Send a confirmation e-mail to the user after create the account
     */
    public function afterCreate()
    {

        /** @var Mailer $mailer */
        $mailer = $this->getDI()->get('mailer');

        $mailer->queue('emailTemplates/confirmation', [
            // Переменные для передачи в шаблон
            'publicUrl' => $this->getDI()->get('config')->application->publicUrl,
            'confirmUrl' => '/confirm/' . $this->code . '/' . $this->user->email,
        ], [
            // Переменные для передачи в
            'address' => [ $this->user->email => $this->user->name ],
            'subject' => "Пожайлоста, подтвердите ваш email!"
        ]);
    }

    public function initialize()
    {
        $this->belongsTo('usersId', __NAMESPACE__ . '\Users', 'id', array(
            'alias' => 'user'
        ));
    }
}
