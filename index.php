<?php

// Set all dates to the UTC
date_default_timezone_set('UTC');

// DB Connection
require_once 'databases/db.php';
// Config
require_once 'config/constants.php';

// Load Middleware
require_once 'middlewares/authentication.php';

// Load Modules
require_once 'modules/JWT.php';

// Load all Models
foreach (glob("models/*.php") as $model) {
    require_once $model;
}

// Load all controllers
foreach (glob("controllers/*.php") as $controller) {
    require_once $controller;
}

// include the Composer autoloader
require_once 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// Connect with the DB
DB::init();

$container = new League\Container\Container;

$container -> share('response', Zend\Diactoros\Response::class);
$container -> share('request', function () {
    // Change this if the "api" is not running on the root of the domain
    // For ex. if this is hosted within a subdir API then set this to "/api"
    $loc = "php-simple-restapi/";
    $_SERVER['REQUEST_URI'] = str_replace($loc, '', $_SERVER['REQUEST_URI']);
    return Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
});

$container -> share('emitter', Zend\Diactoros\Response\SapiEmitter::class);


// Start the Router
$route = new League\Route\RouteCollection;

// Start of the routing definitions
// ----------------------------------------------------------
// Index
$route -> map('GET', '/', function(ServerRequestInterface $request, ResponseInterface $response) {
    $response -> getBody() -> write("Hello World!");
    return $response -> withStatus(200);
});

// User
// Basic Actions
$route -> map('GET', '/users', [new UserController, 'index']) -> middleware($authentication);
$route -> map('GET', '/users/{id}', [new UserController, 'show']) -> middleware($authentication);
$route -> map('PATCH', '/users', [new UserController, 'update']) -> middleware($authentication);
$route -> map('DELETE', '/users', [new UserController, 'delete']) -> middleware($authentication);
// Routes without authentication
$route -> map('POST', '/users/validate_login', [new UserController, 'login']);
$route -> map('POST', '/users', [new UserController, 'register']);


// Dispatch the request to the controller
$response = $route -> dispatch($container -> get('request'), $container -> get('response'));

// sends headers and output using PHP's standard SAPI mechanisms (the header() method and the output buffer)
$container -> get('emitter') -> emit($response -> withHeader('Content-Type', 'application/json'));
