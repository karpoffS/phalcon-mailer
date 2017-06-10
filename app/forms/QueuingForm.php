<?php
namespace Mailer\Forms;

use Mailer\Models\CategoryMessages;
use Mailer\Models\EmailGroups;
use Mailer\Models\MessagesTemplates;
use Mailer\Models\Users;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;

class QueuingForm extends Form
{

    protected $defauls = [
        'name' => 'Обычное имя',
        'description' => 'Обычное описание'
    ];

    public function initialize($entity = null, $options = null)
    {

        $user = $this->getDI()->get('auth')->getUser();

        // ID group
        $id = new Hidden('id');
        $this->add($id);

        // select field by username, used a admin or moderators
//        $users = new Select('user', Users::find('active = 1'), [
//            'using' => [
//                'id',
//                'name'
//            ],
//            'useEmpty' => true,
//            'emptyText' => 'Пользователи',
//            'emptyValue' => ''
//        ]);

//        if(!empty($entity)){
//            $users->setDefault($entity->userId);
//        }

//        $this->add($users);

        $groups = new Select('groupId', EmailGroups::find("userId = {$user->id} AND status = 1"), [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'Выберите список для рассылки',
            'emptyValue' => ''
        ]);

        $this->add($groups);

        $categories = new Select('messageId', MessagesTemplates::find("userId = {$user->id} AND status = 1"), [
            'using' => [
                'id',
                'subject'
            ],
            'useEmpty' => true,
            'emptyText' => 'Выберите сообщение для рассылки',
            'emptyValue' => ''
        ]);

        $this->add($categories);

    }
}
