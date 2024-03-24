<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

use Framework\Router;

/* require basePath('Framework/Database.php');
require basePath('Framework/Router.php'); */

/* $requiredClass = function ($class) {
    $path = basePath('Framework/' . $class . '.php');
    if (file_exists($path)) {
        require $path;
    }
};
spl_autoload_register($requiredClass); */


//Instantiating Router
$router = new Router();
//get routes
$routes = require basePath('routes.php');

//$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


//Route the request
$router->route($uri);
