<?php

set_time_limit(ceil(45*60));
ini_set('memory_limit', '512M');

use Phalcon\Queue\Beanstalk;
use Phalcon\Queue\Beanstalk\Job;
use Phalcon\DI;

//define('APP_PATH', realpath('.'));
define('BASE_DIR', __DIR__);
define('APP_PATH', BASE_DIR."/../../");


/**
 * Read the configuration
 */
$config = include APP_PATH . '/app/config/config.php';

/**
 * Read auto-loader
 */
include APP_PATH . '/app/config/loader.php';

/**
 * Read services
 */
include APP_PATH . '/app/config/services.php';


// Лимит времени перед отправкой сообщений
$secondWait = 2;

// Получаем текущий pid worker-а
$pid = posix_getpid();

// Текущее время
$startTime = time();

// Расчитываем лимит выполение одним процессом
$limitJobs = ceil((60/$secondWait * 60)/4);

// Лимит выполения по времени
$runningTimeWorker = ($limitJobs * $secondWait); // в секудах

$stopTime = strtotime("+$runningTimeWorker seconds", $startTime);

// Считаем кол-во выполненых заданий
$count = 0;

echo "Starting... ".date("d/m/Y H:m:s", $startTime). " Pid: {$pid}".PHP_EOL;
echo "Jobs limit: {$limitJobs}".PHP_EOL;
echo "Time limit: {$runningTimeWorker} seconds".PHP_EOL;
echo "Ready...".PHP_EOL;

/** @var Beanstalk $queue */
$queue = $di->get('queue');
$queue->choose('scheduling');

try{

    while (true) {

        // Остановить по количетву выполненых заданий или по прошествии лимита времени
        if(($count == $limitJobs) || (time() >= $stopTime)) break;

        /** @var Job $job */
        if(($job = $queue->peekReady()) !== false){

            // Лочим задачу на текущем worker-е
            $job->touch();

            $data = json_decode($job->getBody(), true);

            $segments = explode(':', $data['job']);

            if (count($segments) !== 2)
                continue;

            // Выполняем задачу
            call_user_func_array([$di[$segments[0]], $segments[1]], [$job, $data['data']]);

            $count++;
        }

        // Ждём отведённое время перед следующим циклом
        sleep($secondWait);
    }
} catch (\Exception $e ){
    echo $e->getMessage();
    sleep($secondWait*15);
}

