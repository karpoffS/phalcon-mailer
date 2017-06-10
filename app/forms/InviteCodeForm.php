<?php
namespace Mailer\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;


class InviteCodeForm extends Form
{

    protected $defauls = [
        'status' => 1,
    ];

    public function initialize($entity = null, $options = null)
    {

        // ID
        $id = new Hidden('id');
        $this->add($id);

        $email = new Text('email', [
            'placeholder' => 'Email приглашонного'
        ]);

        $email->setLabel("E-mail");

        $email->addValidators([
            new PresenceOf([
                'message' => 'Это поле должно быть заполнено'
            ]),
            new Email([
                'message' => 'email не валидный'
            ])
        ]);

        $this->add($email);
    }
}
