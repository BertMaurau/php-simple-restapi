<?php

// DB Connection
require_once 'app/DB.php';

// Config
require_once 'app/Constants.php';
require_once 'app/Output.php';
require_once 'app/Session.php';

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
