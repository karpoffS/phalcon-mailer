<?php

namespace Mailer\Controllers;

set_time_limit(ceil(30*60));
ini_set('memory_limit', '1024M');

use Mailer\Models\Users;
use Mailer\Parser\ParserService;
use Phalcon\Flash;
use Phalcon\Tag;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\Query\Status;
use Phalcon\Mvc\Model\Message;
use Mailer\Models\EmailImports;
use Mailer\Models\EmailGroups;
use Uploader\Uploader;

/**
 * Отображение главной страницы.
 *
 * @property void auth
*/
class ImportingsController extends ControllerBase
{
    /**
     *  @var \Mailer\Models\Users
     */
    protected $user;

    /**
     * @var \Mailer\Flash\Direct
     */
    protected $flash;

    /**
     * Установка шаблона (layouts/private.volt)
     */
    public function initialize() {
        $this->user = $this->auth->getUser();

        /** @var \Mailer\Flash\Direct $this */
        $this->flash = $this->getDI()->get('flash')->setType('alert');

        $this->view->setTemplateBefore('private');
        parent::initialize();
    }


    /**
     * Получить путь с пользовательской папкой
     * @return string
     */
    public function getPath($groupId = null) {
        if(!empty($groupId)){
            return $this->config->application->uploadsDir.
                $this->user->id.DIRECTORY_SEPARATOR.$groupId;
        }
        return $this->config->application->uploadsDir.$this->user->id;
    }

    /**
     * Действие по умолчанию.
     */
    public function indexAction() {

        $numberPage = 1;

        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Mailer\Models\EmailList', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $lists = EmailImports::find("userId = '". $this->user->id ."'");

        $paginator = new Paginator([
            "data" => $lists,
            "limit" => 25,
            "page" => $numberPage
        ]);

        $this->view->users = Users::find('active = 1');
        $this->view->page = $paginator->getPaginate();

    }

    /**
     * Создание
     */
    public function createAction() {

        $toSave = false;

        if ($this->request->isPost()) {

            $groupId = $this->request->getPost('groupId', 'int');
            $checking = $this->request->getPost('checking', 'int');

            if(!(EmailGroups::findFirstById($groupId))){

                $this->flash->error("Вы не указали группу в которую загружать список!");
//                return $this->dispatcher->forward([ 'action' => 'create' ]);
            } else {
                $toSave = true;
            }
        }

        if ($this->request->hasFiles() == true && $toSave) {

            /** @var Uploader $uploader */
            $uploader = $this->di->get('uploader');

            // Настраиваем загрузчик
            $uploader->setRules([
                'dynamic' => $this->getPath($groupId),
                'minsize' => 10,
                'maxsize' => (10 * 1024 * 1024),
                'extensions' => [
                    'csv'
                ],

                'sanitize' => true,
                'hash' => 'md5'
            ]);

            if ($uploader->isValid() === true) {

                $files = $uploader->move();

                foreach ($files as $file) {

                    $csv = $this->getPath($groupId) . DIRECTORY_SEPARATOR . $file['filename'];

                    // Запускаем парсер
                    if (file_exists($csv)) {

                        // Открываем файл
                        if (($handle = fopen($csv, "r")) !== false) {

                            $totals = 0;

                            // Читаем файл
                            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                                $totals++;
                            }
                        }

                        // Формируем строку вставки
                        $insert = "INSERT INTO Mailer\Models\EmailImports (filename, type, userId, groupId, current, totals, checking, size) VALUES (:filename:, :type:, :userId:, :groupId:, :current:, :totals:, :checking:, :size:)";
//
                        // With bound parameters
                        $manager = $this->modelsManager->createQuery($insert);

                        /** @var Status $status */
                        $status = $manager->execute([
                            'filename' => $file['filename'],
                            'type' => $file['extension'],
                            'userId' => $this->user->id,
                            'groupId' => $groupId,
                            'current' => 0,
                            'totals' => $totals,
                            'checking' => $checking,
                            'size' => $file['size']
                        ]);

                        if ($status->success()) {

                            /** @var EmailImports $import */
                            $import = $status->getModel();

                            /** @var ParserService $parser */
                            $parser = $this->getDI()->get('parsing');

                            if ($jobId = $parser->addToQueueParse($import->getId())) {
//
                                $update = "UPDATE Mailer\Models\EmailImports SET jobId = {$jobId} WHERE id = {$import->getId()}";
                                /** @var Status $statusUpdate */
                                $statusUpdate = $this->modelsManager->executeQuery($update);

                                $startStr = "Отлично, новый список был успешно сохранен";
                                $endStr = "поставлен в очередь на обработку!";

                                if($statusUpdate->success()){
                                    $this->flashSession->notice("{$startStr} и {$endStr}");
                                } else {
                                    $this->flashSession->warning("{$startStr}, но не был {$endStr}");
                                }
                            }

                            return $this->response->redirect("/importings");

                        } else {

                            /** @var Message $message */
                            foreach ($status->getMessages() as $message) {
                                $this->flash->error($message->getMessage());
                            }
                        }
                    }
                }
            } else { // Если что-то пошло не так

                foreach ($uploader->getErrors() as $message) { $this->flash->error($message); }
            }
        }

        /** @var EmailGroups $groups */
        $groups = EmailGroups::find([
            "userId = {$this->user->id} AND status=1", "order" => "name"
        ]);
        if(count($groups) == 0 ){
            $this->flash->warning("У вас нет ни одной группы!");
        }

        $this->view->setVar('groups', $groups);
        $this->view->setVar('checking', [
            1 => 'Проверять на существование доменов',
            0 => 'Не проводить проверки!'
        ]);
    }

    /**
     * Удаление
     *
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function deleteAction($id) {

        /** @var EmailImports $list */
        if ($list = EmailImports::findFirst("id='{$id}'")) {

            $file = $this->getPath(). "/" . $list->getFilename();

            if ($list->delete() == false) {

                $this->flashSession->warning("К сожалению, мы не можем удалить прямо сейчас!");

                foreach ($list->getMessages() as $message) {
                    $this->flashSession->error($message);
                }
            } else {
                if (file_exists($file)){
                    if(unlink($file)){
                        $this->flashSession->success("Список был успешно удален!");
                    } else {
                        $this->flashSession->error("У вас не достаточно прав на удаление списка!");
                    }
                }

                return $this->response->redirect("/importings");

            }
        } else {
            $this->flashSession->error("Список не найден!");
            return $this->response->redirect("/importings");

        }
    }

    /**
     * Остановка отбработки
     * @param $id
     */
    public function pauseAction($id){
        if($list = EmailImports::findFirst("id={$id}")){
            $list->setStatus("queue");
            if($list->save()){
                $this->flash->warning("Обработка списка приостановлена!");
            }
        } else {
            $this->flash->error("Мы не нашли такого списка у нас для того чтобы приостановить его обработку!");

            return $this->dispatcher->forward([ 'action' => 'index' ]);
        }
    }

    /**
     * Запускаем парсинг файла
     * @param $id
     */
    public function parseAction($id){

        if($import = EmailImports::findFirst("id={$id}")){

            /** @var ParserService $parser */
            $parser = $this->getDI()->get('parsing');

            $import->setStatus("done");

            if($jobId = $parser->addToQueueParse($import->getId())){

                $import->setJobId($jobId);

                $this->flash->notice("Список поставлен в очередь на обработку!");
            }

            if($import->save()){

                $this->persistent->searchParams = null;

                return $this->dispatcher->forward([ 'action' => 'index' ]);
            }
        } else {
            $this->flash->error("Мы не нашли такого списка у нас для того чтобы возобновить его обработку!");
            return $this->dispatcher->forward([ 'action' => 'index' ]);
        }
    }
}
