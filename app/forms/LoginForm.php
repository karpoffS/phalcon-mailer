<?php
namespace Mailer\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;

class LoginForm extends Form
{

    public function initialize()
    {

        $translate = $this->getDI()->get("translate");

        // Email
        $email = new Text('email', [
//            'placeholder' => $translate->t('Form-Login-Email')
        ]);

        $email->setLabel($translate->t('Form-Login-Email'));

        $email->addValidators([
            new PresenceOf([
                'message' => $translate->t('Form-Login-Field-Email-Require')
            ]),
            new Email([
                'message' => $translate->t('Form-Login-Field-Email-NotValid')
            ])
        ]);

        $this->add($email);

        // Password
        $password = new Password('password', [
//            'placeholder' => 'Пароль'
        ]);

        $password->setLabel('Пароль');

        $password->addValidator(new PresenceOf([
            'message' => 'Поле "Пароль" должно быть заполнено'
        ]));

        $password->clear();

        $this->add($password);

        // Remember
        $remember = new Check('remember', [
            'value' => 'yes'
        ]);
        $remember->setLabel('Запомнить меня');
        $this->add($remember);

        // Show password
        $showpassword = new Check('showpassword', [
            'value' => 1
        ]);

        $showpassword->setLabel('Показать пароль');

        $this->add($showpassword);

        // CSRF
        $csrf = new Hidden('csrf');

        $csrf->addValidator(new Identical([
            'value' => $this->security->getSessionToken(),
            'message' => 'проверка CSRF не удалось'
        ]));

        $csrf->clear();

        $this->add($csrf);

        $this->add(new Submit('go', array(
            'class' => 'btn btn-success',
            'value' => 'Войти'
        )));
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
