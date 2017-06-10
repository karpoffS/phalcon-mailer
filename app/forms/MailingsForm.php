<?php
namespace Mailer\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Mailer\Models\Users;
use Mailer\Models\EmailGroups;
use Mailer\Auth\Auth;

class MailingsForm extends Form
{

    /**
     * For select answer
     * @var array
     */
    protected $answerYesNo = [ 1 => 'Yes', 0 => 'No' ];

    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {

        /** @var Auth $auth */
        $auth = $this->getDI()->get('auth');

        /** @var Users $user */
        $user = $auth->getUser();

        // id - field
        $id = new Text('id');
        $this->add($id);

        $this->add(new Text('name'));

        if($auth->isPriveleged()){
            $UsersSelect = Users::find([
                "active = 1", "order" => "name"
            ]);
        } else {
            $UsersSelect = Users::find([
                "active = 1 and id=".$user->id, "order" => "name"
            ]);
        }

        $users = new Select('userId', $UsersSelect, [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'By user',
            'emptyValue' => ''
        ]);

        if(!empty($options['userId'])){
            $users->setDefault($options['userId']);
        }

        $this->add($users);


        if($auth->isPriveleged()){

            $and = !empty($options['userId']) ? " AND userId={$options['userId']}" : "";

            $EmailGroupsSelect = EmailGroups::find([
                "status=1".$and, "order" => "name"
            ]);
        } else {
            $EmailGroupsSelect = EmailGroups::find([
                "status=1 and userId=".$user->id, "order" => "name"
            ]);
        }

        $groups = new Select('groupId', $EmailGroupsSelect , [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'By groups',
            'emptyValue' => ''
        ]);


        if(!empty($options['groupId'])){
            $groups->setDefault($options['groupId']);
        }

        $this->add($groups);

        // Confirmed
        $this->add(new Select('confirmed', $this->answerYesNo, [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'By confirmed',
            'emptyValue' => ''
        ]));

        // Unsubscribe
        $this->add(new Select('unsubscribe', $this->answerYesNo, [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'By unsubscribe',
            'emptyValue' => ''
        ]));

        // status
        $this->add(new Select('status', $this->answerYesNo, [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => 'By status',
            'emptyValue' => ''
        ]));

        $email = new Text('address', [
            'placeholder' => 'Email'
        ]);

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'Поле address должно быть заполнено!'
            )),
            new Email(array(
                'message' => 'Поле address не является e-mail адресом'
            ))
        ));

        $this->add($email);


    }
}
