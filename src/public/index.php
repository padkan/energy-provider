<?php
/**
 * Front controller
 */

 //ini_set('session.cookie_lifetime', '864000'); // ten days
/**
 * Twig & ...
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

 /**
 * Manual Autoloader
 */
// spl_autoload_register(function ($class) {
//     $root = dirname(__DIR__);
//     $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
//     if (is_readable($file)) {
//         require $root . '/' . str_replace('\\', '/', $class) . '.php';
//     }

// });

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * session
 */
session_start();

$router = new Core\Router();
//home
$router->add('', ['controller' => 'Home', 'action' => 'index']);
//user
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('forgot', ['controller' => 'Password', 'action' => 'forgotAction']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
//main
$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\d+}');
//auto
$router->add('auto/{controller}/{action}', ['namespace' => 'Auto']);
$router->add('auto/{controller}/{action}/{id:\d+}', ['namespace' => 'Auto']);
//admin 
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->add('admin/{controller}/{action}/{id:\d+}', ['namespace' => 'Admin']);




$router->dispatch($_SERVER['QUERY_STRING']);
