<?php

//
// This will just load all the required files.
//
// Config
// ------------------------------------------------------------------------------
require_once __DIR__ . '/config/config.php';

// DB Connection
// ------------------------------------------------------------------------------
require_once __DIR__ . '/app/DB.php';

// Static classes
// ------------------------------------------------------------------------------
require_once __DIR__ . '/app/Auth.php';
require_once __DIR__ . '/app/Env.php';
require_once __DIR__ . '/app/Output.php';

// Load Middleware
// ------------------------------------------------------------------------------
require_once __DIR__ . '/middlewares/authentication.php';

// Load Modules
// ------------------------------------------------------------------------------
foreach (glob(__DIR__ . '/modules/*.php') as $module) {
    require_once $module;
}
// Load all Models
// ------------------------------------------------------------------------------
foreach (glob(__DIR__ . '/models/*.php') as $model) {
    require_once $model;
}

// Load all controllers
// ------------------------------------------------------------------------------
foreach (glob(__DIR__ . '/controllers/*.php') as $controller) {
    require_once $controller;
}

// include the Composer autoloader for external dependencies
// ------------------------------------------------------------------------------
require_once __DIR__ . '/vendor/autoload.php';
