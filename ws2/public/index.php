<?php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Router;

define('ROOT', dirname(__DIR__) . '/' );

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$router = new Router();

$router->handle($_SERVER);
