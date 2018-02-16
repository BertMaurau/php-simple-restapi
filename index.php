<?php

// Handle CORS
// Modify to your own specifications
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, X-PINGOTHER, Origin, X-Requested-With, Content-Type, Accept, Cache-Control, Pragma, Accept-Encoding');
header('Access-Control-Max-Age: 1728000');

// If the clientside requests a preflight OPTIONS request due to custom headers or some sort
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

// Set all dates to the UTC
date_default_timezone_set('UTC');

require 'loader.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// Connect with the DB
try {
    DB::init();
} catch (Exception $ex) {
    echo "Failed to connect with DB. Reason: " . $ex -> getMessage();
    exit;
}

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
    return Output::OK($response, "Hello World!");
});

// User
// Basic Actions
// ----------------------------------------------------------------
// Get the currently logged-in user
$route -> map('GET', '/me', [new UserController, 'me']) -> middleware($authentication);

// List all Users (Paginate with take/skip parameters)
$route -> map('GET', '/users', [new UserController, 'index']) -> middleware($authentication);

// Get a specific User by ID
$route -> map('GET', '/users/{id}', [new UserController, 'show']) -> middleware($authentication);

// |----------------------------------------------------------------------
// | Remark: Next two routes shouldn't be available for default Users.
// | This could allow them to update or delete another user.
// | Depends on your situation ofcourse
// |----------------------------------------------------------------------
// Update a specific User by ID
$route -> map('PATCH', '/users/{id}', [new UserController, 'update']) -> middleware($authentication);

// Get a specific User by ID
$route -> map('DELETE', '/users/{id}', [new UserController, 'delete']) -> middleware($authentication);

// Routes without authentication
// ----------------------------------------------------------------
// Validate the given Login
$route -> map('POST', '/users/validate_login', [new UserController, 'login']);

// Register a new User
$route -> map('POST', '/users', [new UserController, 'register']);

// Other Examples
// ----------------------------------------------------------------
// Group middleware
// $route -> group('/customers', function ($route) {
//
//     $route -> map('GET', '/', [new CustomerController, 'index']);
//     $route -> map('POST', '/', [new CustomerController, 'create']);
//     //...
//
// }) -> middleware($authentication);
// Dispatch the request to the controller
$response = $route -> dispatch($container -> get('request'), $container -> get('response'));

// sends headers and output using PHP's standard SAPI mechanisms (the header() method and the output buffer)
$container -> get('emitter') -> emit($response -> withHeader('Content-Type', 'application/json'));
