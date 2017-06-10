<?php
namespace Mailer\Controllers;

use Mailer\Forms\MessagesTemplatesForm;
use Mailer\Models\CategoryMessages;
use Mailer\Models\MessagesTemplates;
use Phalcon\Mvc\Model\Criteria;
use Mailer\Models\Users;
use Phalcon\Flash;
use Phalcon\Tag;

use Phalcon\Paginator\Adapter\Model as Paginator;

/**
 * Отображение главной страницы.
 */
class MessagesController extends ControllerBase
{
    /**
     *  @var \Mailer\Models\Users
     */
    protected $user;

    /**
     * Установка шаблона (layouts/private.volt)
     */
    public function initialize() {
        $this->user = $this->auth->getUser();

        $this->view->setTemplateBefore('private');
        parent::initialize();
    }

    /**
     * Получаем путь
     *
     * @param $userId
     * @return string
     */
    private function getPath($userId){

        return $this->config->application->emailTemplateDir . $userId . DIRECTORY_SEPARATOR;
    }

    /**
     * Получаем имя файла с расширением
     *
     * @param array  $data
     * @param string $ext
     * @return string
     */
    private function getFilename($data, $ext = ".volt") {

        // Вычесляем хеш файла
        $hash = md5($data['userId'] . $data['categoryId'] . $data['messageId']);

        return $hash . $ext;
    }

    /**
     * @param      $data
     * @param null $id
     * @return bool
     */
    private function saveTemplate($data, $id = null){

        if(!empty($id)){
            $data['messageId'] = $id;
        }

        // Получаем пусть сохранения
        $source_path = $this->getPath($data['userId']);

        // Проверяем путь
        if (!file_exists($source_path)){

            // Если нету полного пути то создаём
            if(!mkdir($source_path, 0777 , true)){
                $this->flash->error("У вас нет прав доступа на создание директории ({$source_path})");
            }
        }

        // Получаем полный путь
        $source_file = $source_path . $this->getFilename($data);

        // Проверяем на существование
        if (file_exists($source_file)) {

            // Если есть то стиравем предыдущий
            if(!unlink($source_file)){
                $this->flash->error("У вас нет прав доступа на записть файла ({$source_file})");
            }
        }

        // открываем файл
        if (!$file_write = fopen($source_file, 'a')) {
            $this->flash->error("Не могу открыть файл ({$source_file})");
        }

        // Сохраняем данные
        if ($status = fwrite($file_write, $data['body']) === FALSE) {
            $this->flash->error("У вас нет прав доступа на записть файла ({$source_file})");
        }

        // Закрываем соединение
        fclose($file_write);

        return file_exists($source_file);
    }

    /**
     * Действие по умолчанию.
     */
    public function indexAction()
    {
        $this->persistent->conditions = null;

        $numberPage = 1;

        if ($this->request->isPost()) {
            $query = Criteria::fromInput(
                $this->di,
                'Mailer\Models\MessagesTemplates',
                $this->request->getPost()
            );
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        if($this->auth->isPriveleged()){
            $parameters = [];
        } else {
            $parameters = ['userId = '.$this->user->id ];
        }

        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $messages = MessagesTemplates::find($parameters);

        if(count($messages) == 0 ){
            $this->flash->warning("У вас нет ни одного составленого шаблона!");
        }

        $paginator = new Paginator([
            "data"  => $messages,
            "limit" => 10,
            "page"  => $numberPage
        ]);

        $this->view->setVar('users', Users::find('active = 1'));

        if($this->auth->isPriveleged()){
            $this->view->setVar('categories', CategoryMessages::find());
        } else {
            $this->view->setVar('categories', CategoryMessages::find('userId = '.$this->user->id));
        }

        $this->view->setVar('page', $paginator->getPaginate());
    }

    /**
     * Создание
     */
    public function createAction()
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        if ($this->request->isPost()) {

            $messages = new MessagesTemplates();

            if($this->auth->isPriveleged()){
                $userId = $this->request->getPost('user', 'int');
            } else {
                $userId = $this->user->id;
            }

            $message = [
                'userId' => $userId ,
                'categoryId' => $this->request->getPost('categoryId', 'int'),
                'subject' => $this->request->getPost('subject', 'striptags'),
                'body' => $this->request->getPost('body'),
                'status' => $this->request->getPost('status', 'int')
            ];

            $messages->assign($message);

            if (!$messages->save()) {
                $this->flash->error($messages->getMessages());
            } else {

                $this->saveTemplate($message, $messages->getId());
                $this->flash->success("Сообщение успешно создано!");

                Tag::resetInput();
            }
        }

        $this->view->setVar('form', new MessagesTemplatesForm(null));
    }


    /**
     * Создание
     *
     * @param $id
     */
    public function editAction($id)
    {
        $this->persistent->conditions = null;
        $this->persistent->searchParams = null;

        $messages = MessagesTemplates::findFirstById($id);
        if (!$messages) {

            $this->flash->error("Сообщение не найдено!");
            return $this->dispatcher->forward([ 'action' => 'index' ]);
        }

        if ($this->request->isPost()) {

            if($this->auth->isPriveleged()){
                $userId = $this->request->getPost('user', 'int');
            } else {
                $userId = $this->user->id;
            }

            $message = [
                'userId' => $userId ,
                'categoryId' => $this->request->getPost('categoryId', 'int'),
                'subject' => $this->request->getPost('subject', 'striptags'),
                'body' => $this->request->getPost('body'),
                'status' => $this->request->getPost('status', 'int')
            ];

            $messages->assign($message);

            if (!$messages->save()) {
                $this->flash->error($messages->getMessages());
            } else {

                $this->saveTemplate($message, $messages->getId());

                $this->flash->success("Данные были успешно обновлены!");

                Tag::resetInput();
            }
        }

        $this->view->setVar('message', $messages);
        $this->view->setVar('form', new MessagesTemplatesForm($messages, [
            'edit' => true
        ]));
    }

    /**
     * Удаление
     *
     * @param $id
     */
    public function deleteAction($id){

        /** @var MessagesTemplates $message */
        $message = MessagesTemplates::findFirstById($id);

        if (!$message) {
            $this->flash->error("Сообщение не найдено!");

            return $this->dispatcher->forward([ 'action' => 'index' ]);
        }

        if (!$message->delete()) {
            $this->flash->error($message->getMessages());
        } else {

//            // Получаем пусть сохранения
//            $source_path = $this->getPath($message->getUserId());
//
//            // Проверяем путь
//            if (!file_exists($source_path)){
//                $this->flash->error("У вас нет прав доступа к директории ({$source_path})");
//            }
//
//            // Получаем полный путь
//            $source_file = $source_path . $this->getFilename([
//                    'userId' => $message->getUserId(),
//                    'categoryId' => $message->getCategoryId(),
//                    'messageId' => $id
//                ]);
//
//            // Проверяем на существование
//            if (file_exists($source_file)) {
//
//                // Если есть то стиравем предыдущий
//                if(!unlink($source_file)){
//                    $this->flash->error("У вас нет прав доступа на удаление файла ({$source_file})");
//                } else {
//                    $this->flash->success("Сообщение удалено!");
//                }
//
//            } else {
//                $this->flash->error("Отсутствует файл по указанному пути ({$source_file})");
//            }

            $this->flash->success("Сообщение удалено!");


        }

        return $this->dispatcher->forward([ 'action' => 'index' ]);
    }
}
