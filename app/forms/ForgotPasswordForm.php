<?php
namespace Mailer\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

class ForgotPasswordForm extends Form
{

    public function initialize()
    {
        $email = new Text('email', [
            'placeholder' => 'Email'
        ]);

        $email->setLabel('Email');

        $email->addValidators([
            new PresenceOf([
                'message' => 'Требуется электронная почта'
            ]),
            new Email([
                'message' => 'Адрес электронной почты не действителен'
            ])
        ]);

        $this->add($email);

        $this->add((new Submit(
            'Send',
            ['class' => 'btn btn-primary']
        )));
    }
}
