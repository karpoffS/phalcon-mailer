<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router();

// Авторизация
$router->add('/login', array(
    'controller' => 'session',
    'action' => 'login'
));

// Регистрация
$router->add('/signup', array(
    'controller' => 'session',
    'action' => 'signup'
));


// Подтверждение почты
$router->add('/confirm/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'confirmEmail'
));

// Сброс Пароля
$router->add('/reset-password/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'resetPassword'
));

return $router;
