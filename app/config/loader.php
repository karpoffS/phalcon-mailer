<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'Mailer\Models'      => $config->application->modelsDir,
    'Mailer\Controllers' => $config->application->controllersDir,
    'Mailer\Forms'       => $config->application->formsDir,
    'Mailer'             => $config->application->libraryDir
]);

$loader->register();

require_once __DIR__ . '/../helpers/functions.php';

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../vendor/autoload.php';
