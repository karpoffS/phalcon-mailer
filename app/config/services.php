<?php

use Mailer\Parser\ParserService;
use Mailer\Scheduler\SchedulerService;
use Phalcon\DI\FactoryDefault;
use Phalcon\Filter;
use Phalcon\Crypt;

use Phalcon\Events\Manager as EventManager;

use Mailer\Flash\Direct as Flash;

use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

//use Phalcon\Session\Adapter\Files;
use Phalcon\Session\Adapter\Redis;
use Phalcon\Queue\Beanstalk;

use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;

use Mailer\Auth\Auth;
use Mailer\Acl\Acl;
use Mailer\Mailings\MailerService;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

//var_dump($config->toArray());

/**
 * Компонент, который позволяет управлять статическими ресурсами, такие как таблицы стилей CSS или JavaScript-библиотеки в веб-приложении
 */
$di->set('assets', function (){
    return new \Phalcon\Assets\Manager();
}, true);

/**
 * Компонент URL-адрес используется, чтобы генерировать все виды URL-адреса в приложении
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);

/**
 * Translate
 * Add translate service (for workers, I set only one file for translate, you can add switch):
 */
$di->set('translate', function() use ($config) {

    // Получение оптимального языка из браузера
    $language = (new Phalcon\Http\Request())->getBestLanguage();

    // Проверка существования перевода для полученного языка
    if (file_exists($config->application->messagesDir. "/" . $language . ".php")) {
        $messages = require $config->application->messagesDir. "/" . $language . ".php";
    } else {
        // Переключение на язык по умолчанию
        $messages = require $config->application->messagesDir. "/" .
            $config->application->defaultLanguage . ".php";
    }

    // Возвращение объекта работы с переводом
    return new \Phalcon\Translate\Adapter\NativeArray(array(
        "content" => $messages
    ));
});

/**
 * Настройка компонента представления
 */
$di->set('view', function () use ($config, $di) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir . 'volt/',
                'compiledSeparator' => '_'
            ));

            $compiler = $volt->getCompiler();

            $compiler->addFunction('strtotime','strtotime');
            $compiler->addFunction('number_format','number_format');
            $compiler->addFunction('trim','trim');
            $compiler->addFilter('e_char',function($resolvedArgs,$exprArgs){
                return 'htmlspecialchars('.$resolvedArgs.')';
            });

            // Register filter
            $compiler->addFilter('t', function($resolvedArgs, $exprArgs) use ($di) {
                return '$this->getDI()->get("translate")->t(' . $resolvedArgs . ')';
            });

            $compiler->addFunction(
                'contains_text',
                function ($resolvedArgs, $exprArgs) {
                    if (function_exists('mb_stripos')) {
                        return 'mb_stripos(' . $resolvedArgs . ')';
                    } else {
                        return 'stripos(' . $resolvedArgs . ')';
                    }
                }
            );

            $compiler->addFunction(
                'preg_match',
                function ($resolvedArgs, $exprArgs) {
                    return 'preg_match(' . $resolvedArgs . ')';
                }
            );

            //register functions
//            $functions = new ReflectionClass('Utils\Volt\Functions');
//            $methods = $functions->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC);
//            foreach($methods as $method) {
//                $name = $method->getName();
//                $volt->getCompiler()->addFunction($name, function($resolvedArgs, $exprArgs) use ($name) {
//                    return '\Utils\Volt\Functions::' . $name . '(' . $resolvedArgs . ')';
//                });
//            }

            // Register filters
//            $filters = new ReflectionClass('Utils\Volt\Filters');
//            $methods = $filters->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC);
//            foreach($methods as $method) {
//                $name = $method->getName();
//                $volt->getCompiler()->addFilter($name, function($resolvedArgs, $exprArgs) use ($name) {
//                    return '\Utils\Volt\Filters::' . $name . '(' . $resolvedArgs . ')';
//                });
//            }

            return $volt;
        },

        '.phtml' => function($view, $di){
            return new \Phalcon\Mvc\View\Engine\Php($view, $di);
        }
    ));

    return $view;
}, true);


/**
 * Подключение к базе данных создается на основе параметров, заданных в файле конфигурации
 */
$di->set('db', function () use ($config, $di) {

    $db = new Mysql(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    ));

    if ($config->database->profiler) {

        $eventsManager = new EventManager();

        $profiler = new \Phalcon\Db\Profiler();
        $di->set('profiler', $profiler);

        $eventsManager->attach('db', function ($event, Mysql $db) use ($profiler, $config) {

            if ($event->getType() == 'beforeQuery') {
                $profiler->startProfile($db->getSQLStatement());

                if ($config->database->profilerPrint) {
                    var_dump([
                        'SQLStatement' => $db->getSQLStatement(),
                        'SqlVariables' => $db->getSqlVariables(),
                        'DefaultValue' => $db->getDefaultValue()
                    ]);
                }
            }

            if ($event->getType() == 'afterQuery') {
                $profiler->stopProfile();
            }
        });

        $db->setEventsManager($eventsManager);

    }

    return $db;
});

/**
 * Менеджер транзакций
 */
$di->setShared('transactions', function () {
    return new TransactionManager();
});

/**
 * Промежуточные данные моделей
 */
$di->set('modelsMetadata', function () use ($config) {

    // 0 - Memory, 1 - Session, 2 - Redis, 3 - Apc, 4 - XCache, 5 - Files
    switch($config->application->metadata){
        case 1:
                $metadata = new \Phalcon\Mvc\Model\Metadata\Session([
                    'prefix' => $config->application->id
                ]);
            break;
        case 2:
                $metadata = new \Phalcon\Mvc\Model\Metadata\Redis([
                    'host'       => '127.0.0.1',
                    'port'       => 6379,
                    'persistent' => 0,
                    'statsKey'   => '_PHCM_MM',
                    'lifetime'   => 172800,
                    'index'      => 2
                ]);
            break;

        case 3:
                $metadata = new \Phalcon\Mvc\Model\Metadata\Apc([
                    'prefix' => $config->application->id,
                    'lifetime' => 86400
                ]);
            break;
        case 4:
                $metadata = new \Phalcon\Mvc\Model\Metadata\Xcache([
                    'prefix' => $config->application->id,
                    'lifetime' => 86400
                ]);
            break;
        case 5:
                $metadata = new \Phalcon\Mvc\Model\Metadata\Files([
                    'metaDataDir' => $config->application->cacheDir . 'metaData/'
                ]);
            break;
        default:
                $metadata = new \Phalcon\Mvc\Model\Metadata\Memory();
            break;
    }

    return $metadata;
});


/**
 * Регистрируем в контейнере сервис "filter"
 */
$di->set('filter', function () {
    $filter = new Filter();
    return $filter;
});


/**
 * Сервис сессий
 */
$di->set('session', function() use ($config) {
    $session = new Redis([
        'path'     => $config->sessions->pathhost,
        'lifetime' => $config->sessions->lifetime,
        'statsKey' => 'sessions',
    ]);
    $session->start();
    return $session;
});

/**
 * Сервис шифрования
 */
$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * Подключаем диспетчер
 */
$di->set('dispatcher', function () use ($config) {

    $dispatcher = new Dispatcher();

    // Навешиваем обработчик ошибок
    if($config->application->handlerErrors) {
        $eventsManager = new EventManager();
        $eventsManager->attach("dispatch", function($event, Dispatcher $dispatcher, $exception) {
            if ($event->getType() == 'beforeException') {
                switch ($exception->getCode()) {
                    case $dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case $dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    case $dispatcher::EXCEPTION_NO_DI:
                    case $dispatcher::EXCEPTION_CYCLIC_ROUTING:
                    case $dispatcher::EXCEPTION_INVALID_HANDLER:
                    case $dispatcher::EXCEPTION_INVALID_PARAMS:
                        $dispatcher->forward([
                            'namespace'  => 'Mailer\Controllers',
                            'controller' => 'errors',
                            'action'     => 'show404'
                        ]);
                        return false;
                }
            }
        });

        $dispatcher->setEventsManager($eventsManager);
    }

    $dispatcher->setDefaultNamespace('Mailer\Controllers');
    return $dispatcher;
});

/**
 * Маршруты в файле routes.php
 */
$di->set('router', function () {
    return require __DIR__ . '/routes.php';
});

/**
 * Настройка классов всплывающих подсказок
 */
$di->set('flash', function () {

    return new Flash([
        'error' => '',
        'success' => '',
        'notice' => '',
        'warning' => ''
    ]);

//    return new Flash([
//        'error' => 'alert alert-danger',
//        'success' => 'alert alert-success',
//        'notice' => 'alert alert-info',
//        'warning' => 'alert alert-warning'
//    ]);

});


/**
 * Настройка классов Session всплывающих подсказок
 */
$di->set('flashSession', function () {
    return (new Phalcon\Flash\Session)->setCssClasses([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Компонент Авторизации
 */
$di->set('auth', function () {
    return new Auth();
});

/**
 * Сервис работы с upload файломи
 */
$di->set('uploader', '\Uploader\Uploader');


/**
 * Сервис работы с csv файлами
 */
$di->set('parsing', function () {
    $parser = new ParserService();
    return $parser;
});


/**
 * Сервис работы с csv файлами
 */
$di->set('scheduler', function () {
    $scheduler = new SchedulerService();
    return $scheduler;
});


/**
 * Компонент очереди
 */
$di->set('queue', function () {
    $queue = new Beanstalk();
    $queue->connect();
//    $queue->choose('mailer');
    return $queue;
});

/**
 * Сервис очередей рассылки почты
 */
$di->set('mailer', function () {
    $service = new MailerService();
    return $service->mailer();
});

/**
 * Access Control List
 * Сервси Контроля доступа
 */
$di->set('acl', function () {
    return new Acl();
});

/**
 * Logger service
 * Сервис логирования
 */
$di->set('logger', function ($filename = null, $format = null) use ($config) {
    $format   = $format ?: $config->get('logger')->format;
    $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
    $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new FormatterLine($format, $config->get('logger')->date);
    $logger    = new FileLogger($path . $filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});
