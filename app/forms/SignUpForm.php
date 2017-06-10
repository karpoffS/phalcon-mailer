<?php
namespace Mailer\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class SignUpForm extends Form
{

    public function initialize($entity = null, $options = null)
    {
        $name = new Text('name', [
            'placeholder' => "Ф.И.О."
        ]);

        $name->setLabel('Имя');

        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'The name is required'
            ))
        ));

        $this->add($name);

        // Email
        $email = new Text('email', [
        'placeholder' => "example@mail.com"
        ]);

        $email->setLabel('E-Mail');

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'The e-mail is required'
            )),
            new Email(array(
                'message' => 'The e-mail is not valid'
            ))
        ));

        $this->add($email);

        // Password
        $password = new Password('password', [
            'placeholder' => "Введите пароль"
        ]);

        $password->setLabel('Пароль');

        $password->addValidators([
            new PresenceOf([
                'message' => 'Необходими ввести пароль!'
            ]),
            new StringLength([
                'min' => 8,
                'messageMinimum' => 'Пароль слишком короткий. Минимум 8 символов'
            ]),
//            new Confirmation([
//                'message' => 'Пароли не совпадают',
//                'with' => 'confirmPassword'
//            ])
        ]);

        $this->add($password);

        // Invitation code
        $InvitationCode = new Text('invitationCode', [
            'placeholder' => "Пример кода 7kDuSFT4dz4"
        ]);

        $InvitationCode->setLabel('Код приглашения');

        $InvitationCode->addValidators(array(
            new PresenceOf(array(
                'message' => 'Введите код приглашения'
            ))
        ));

        $this->add($InvitationCode);

        // terms
        $terms = new Check('terms', array(
            'value' => 'yes'
        ));

        $terms->setLabel('Я ознакомлен(а) с правилами и договором!');

        $terms->addValidator(new Identical(array(
            'value' => 'yes',
            'message' => 'Ознакомтесь с правилами и договором!'
        )));

        $this->add($terms);

        // CSRF
        $csrf = new Hidden('csrf');

        $csrf->addValidator(new Identical([
            'value' => $this->security->getSessionToken(),
            'message' => 'Форма не действительна'
        ]));

        $this->add($csrf);

        $signup = new Submit('Sign Up', array(
            'class' => 'btn btn-warning'
        ));

        $signup->setAttribute('value', 'Регистрация');

        // Sign Up
        $this->add($signup);
    }

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if ($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }
}
