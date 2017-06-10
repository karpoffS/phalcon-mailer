<?php
namespace Mailer\Forms;

use Mailer\Auth\Auth;
use Mailer\Models\CategoryMessages;
use Mailer\Models\MessagesTemplates;
use Mailer\Models\Users;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;

class MessagesTemplatesForm extends Form
{

    protected $defauls = [
        'status' => 1,
        'name' => 'Обычное имя',
        'description' => 'Обычное описание'
    ];

    /**
     * @param MessagesTemplates|null $entity
     * @param null                   $options
     * @throws \Mailer\Auth\Exception
     */
    public function initialize(MessagesTemplates $entity = null, $options = null)
    {

        /** @var Auth $auth */
        $auth = $this->getDI()->get('auth');

        /** @var Users $user */
        $user = $auth->getUser();

        // ID group
        $id = new Hidden('id');
        $this->add($id);

        // text field by subject
        $subject = new Text('subject', [
            'placeholder' => 'Тема письма'
        ]);
        $subject->setLabel('Тема письма');

        $subject->addValidators([
            new PresenceOf([
                'message' => 'Тема письма должна быть заполнена!'
            ])
        ]);
        $this->add($subject);


        // text area field by body
        $body = new Textarea('body', [
            'placeholder' => 'Тело письма'
        ]);
        $body->setLabel('Тело письма');
        $body->addValidators([
            new PresenceOf([
                'message' => 'Тело письма должно быть заполнено!'
            ])
        ]);
        $this->add($body);


        // select field by category, used a admin or moderators
        $userId = !empty($entity) ? $entity->userId : $user->id;

        $category = new Select('categoryId', CategoryMessages::find('userId = '.$userId), [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'Выберите категорию...',
            'emptyValue' => ''
        ]);
        $category->setLabel('Категоря сообщения');

        if(!empty($entity)) $category->setDefault($entity->categoryId);

        $this->add($category);


        // Редактирвание даты создания
        $createdAt = new Date('createdAt');
        $createdAt->setLabel('Дата создания');
        $this->add($createdAt);

        // Редактирвание даты модификации
        $modifyAt = new Date('modifyAt');
        $modifyAt->setLabel('Дата редактирования');
        $this->add($modifyAt);


        // select field by username, used a admin or moderators
        $users = new Select('user', Users::find('active = 1'), [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'Пользователи',
            'emptyValue' => ''
        ]);
        $users->setLabel('Пользователь');

        if(!empty($entity)) $users->setDefault($entity->userId);

        $this->add($users);


        // select field by status group
        $this->add((new Select('status', [
            1 => 'Вкл',
            0 => 'Выкл'
        ]))->setDefault($this->defauls['status']));
    }
}
