<?php

//
// This will just load all the required files.
//
// Config
// ------------------------------------------------------------------------------
require_once 'config/config.php';

// DB Connection
// ------------------------------------------------------------------------------
require_once 'app/DB.php';

// Static classes
// ------------------------------------------------------------------------------
require_once 'app/Auth.php';
require_once 'app/Env.php';
require_once 'app/Output.php';

// Load Middleware
// ------------------------------------------------------------------------------
require_once 'middlewares/authentication.php';

// Load Modules
// ------------------------------------------------------------------------------
require_once 'modules/JWT.php';

// Load all Models
// ------------------------------------------------------------------------------
foreach (glob("models/*.php") as $model) {
    require_once $model;
}

// Load all controllers
// ------------------------------------------------------------------------------
foreach (glob("controllers/*.php") as $controller) {
    require_once $controller;
}

// include the Composer autoloader for external dependencies
// ------------------------------------------------------------------------------
require_once 'vendor/autoload.php';
