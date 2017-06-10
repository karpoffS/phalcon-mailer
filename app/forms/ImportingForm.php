<?php
namespace Mailer\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Mailer\Models\Users;

class ImportingForm extends Form
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

        // In edition the id is hidden
        if (isset($options['edit']) && $options['edit']) {
            $id = new Hidden('id');
        } else {
            $id = new Text('id');
        }

        $this->add($id);

        $this->add(new Text('userId', Users::find('active = 1')));

        $this->add(new Text('name'));

//        $this->add(new Select('userId', Users::find(), [
//            'using' => [
//                'id',
//                'name'
//            ],
//            'useEmpty' => true,
//            'emptyText' => 'By user',
//            'emptyValue' => ''
//        ]));

//        $this->add(new Select('groupId', EmailGroups::find("status = 1"), [
//            'using' => [
//                'id',
//                'name'
//            ],
//            'useEmpty' => true,
//            'emptyText' => 'By groups',
//            'emptyValue' => ''
//        ]));



    }
}
