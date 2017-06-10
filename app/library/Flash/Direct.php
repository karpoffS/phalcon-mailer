<?php

namespace Mailer\Flash;


class Direct extends \Phalcon\Flash\Direct {

    /**
     * @var string
     */
    protected $typeMessage = 'form';

    /**
     * @var boolean
     */
    protected $isDebugMessage = false;

    /**
     * Установка типа сообщения
     * @param $type
     * @return $this
     */
    public function setType($type){
        switch($type){
            case 'alert':
                $this->typeMessage = 'alert';
                break;
            default:
                $this->typeMessage = 'form';
                break;
        }
        return $this;
    }

    /**
     * Установка режима дебаг
     * @param $bool
     * @return $this
     */
    public function setDebug($bool) {
        $this->isDebugMessage = (bool) $bool;
        return $this;
    }

    /**
     * @param string $type
     * @param mixed  $message
     */
    public function message($type, $message) {

        if($this->isDebugMessage){
            var_dump([$type, $message]);
        }


        $map['alert'] = [
            'default' => [ 'class' => 'alert-warning', 'title' => 'Внимание' ],
            'error' => [ 'class' => 'alert-danger', 'title' => 'Опасность' ],
            'success' => [ 'class' => 'alert-success', 'title' => 'Выполнено' ],
            'notice' => [ 'class' => 'alert-info', 'title' => 'Информация' ],
            'warning' => [ 'class' => 'alert-warning', 'title' => 'Внимание' ]
        ];

        $map['form'] = [
            'default' => [ 'class' => 'alert alert-warning' ],
            'error' => [ 'class' => 'alert alert-danger' ],
            'success' => [ 'class' => 'alert alert-success' ],
            'notice' => [ 'class' => 'alert alert-info' ],
            'warning' => [ 'class' => 'alert alert-warning' ]
        ];

        if($this->typeMessage === 'alert'){
//            $title = isset($map[$this->typeMessage][$type]) ? $map[$this->typeMessage][$type]['title'] : $map[$this->typeMessage]['default']['title'];

            $class = isset($map[$this->typeMessage][$type]) ? $map[$this->typeMessage][$type]['class'] : $map[$this->typeMessage]['default']['class'];

            $message = '<div class="alert alert-dismissible ' . $class .'">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <p>'.$message.'</p>
                    </div>';
        }

        if($this->typeMessage === 'form') {

            $class = isset($map[$this->typeMessage][$type]) ? $map[$this->typeMessage][$type]['class'] : $map[$this->typeMessage]['default']['class'];

            if(is_array($message)){
                foreach($message as $msg){
                    $message = '<div class="' . $class .'" role="alert">'.$msg.'</div>';
                }
            } else {
                $message = '<div class="' . $class .'" role="alert">'.$message.'</div>';
            }
        }

        echo $message;
        return $message;
//        parent::message($type, $message);
    }
}