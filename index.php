<?php

/**
 * ==============================================================================
 *
 * A simple and basic PHP RESTful API seed.
 *
 * PHP version >= 5.4
 *
 * @category API's
 * @author   Bert Maurau <hello@bertmaurau.be>
 * @license  MIT
 * @link     https://github.com/BertMaurau/php-simple-restapi
 *
 * ==============================================================================
 */
// ------------------------------------------------------------------------------
// Handle CORS
// ------------------------------------------------------------------------------
// This must be done before any output has been sent from the server..
// Modify to your own specifications
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: '
        . 'POST, '
        . 'GET, '
        . 'DELETE, '
        . 'PUT, '
        . 'PATCH, '
        . 'OPTIONS');
header('Access-Control-Allow-Headers: '
        . 'Authorization, '
        . 'X-PINGOTHER, '
        . 'Origin, '
        . 'X-Requested-With, '
        . 'Content-Type, '
        . 'Accept, '
        . 'Cache-Control, '
        . 'Pragma, '
        . 'Accept-Encoding');
header('Access-Control-Max-Age: 1728000');

// If the clientside requested a preflight OPTIONS request due to custom headers
// of some sort.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    // end the script here
    die();
}

// Set all dates to the UTC (default)
// Set this to your own needs or comment it out.
// You can get the list of all supported timezones here:
//  - http://php.net/manual/en/timezones.php
// ------------------------------------------------------------------------------
date_default_timezone_set('UTC');

// Load all the required classes and files.
// You can create a specific App class to handle all the App functionalities,
// but just to get you started..
// ------------------------------------------------------------------------------
require 'loader.php';

// Use these specific namespace classes for the whole routing process.
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// Connect with the DB
// This could also be within some sort of App class.
// See the DB class to get the list of available functions
// ------------------------------------------------------------------------------
try {
    DB::init();
} catch (Exception $ex) {
    echo "Failed to connect with DB. Reason: " . $ex -> getMessage();
    // No DB, no API.
    exit;
}

// ------------------------------------------------------------------------------
// Start the routing build-up
// ------------------------------------------------------------------------------
$container = new League\Container\Container;

$container -> share('response', Zend\Diactoros\Response::class);
$container -> share('request', function () {
    // Change Constants API ROOT if the "api" is not running on the root of the
    // domain. For ex. if this is hosted within a subdir API then set this
    // to "/api" so that it matches the actual url: http://domain.com/api for ex.
    $_SERVER['REQUEST_URI'] = str_replace(API_ROOT, '', $_SERVER['REQUEST_URI']);
    return Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
});

$container -> share('emitter', Zend\Diactoros\Response\SapiEmitter::class);


// Start the Router
$route = new League\Route\RouteCollection;

// require the defined routes
require __DIR__ . '/routes.php';

$response = $route -> dispatch(
        $container -> get('request'), $container -> get('response'));

// sends headers and output using PHP's standard SAPI mechanisms (the header()
// method and the output buffer)
$container -> get('emitter') -> emit(
        $response -> withHeader('Content-Type', 'application/json'));

// ------------------------------------------------------------------------------
// end of script
