<?php


require __DIR__ . '/../vendor/autoload.php';


use Framework\Router;
use Framework\Session;

Session::start();

require '../helpers.php';

//Instantiating Router
$router = new Router();
//get routes
$routes = require basePath('routes.php');

//$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


//Route the request
$router->route($uri);
