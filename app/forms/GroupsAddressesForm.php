<?php
namespace Mailer\Forms;

use Mailer\Models\Users;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;

class GroupsAddressesForm extends Form
{

    protected $defauls = [
        'status' => 1,
        'name' => 'Обычное имя',
        'description' => 'Обычное описание'
    ];

    public function initialize($entity = null, $options = null)
    {

        // In edition the id is hidden
//        if (isset($options['edit']) && $options['edit']) {
//            $id = new Hidden('id');
//        } else {
//        }

        // ID group
        $id = new Hidden('id');
        $this->add($id);

        // text field by name
        $name = new Text('name', [
            'placeholder' => 'Имя группы'
        ]);
        $name->addValidators([
            new PresenceOf([
                'message' => 'Имя должно быть заполнено!'
            ])
        ]);
        $this->add($name);

        // text field by description
        $description = new Text('description', [
            'placeholder' => 'Описание группы'
        ]);
        $description->addValidators([
            new PresenceOf([
                'message' => 'Описание должно быть заполнено!'
            ])
        ]);
        $this->add($description);


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

        if(!empty($entity)){
            $users->setDefault($entity->userId);
        }

        $this->add($users);

        // select field by status group
        $this->add((new Select('status', [
            1 => 'Вкл',
            0 => 'Выкл'
        ]))->setDefault($this->defauls['status']));
    }
}
