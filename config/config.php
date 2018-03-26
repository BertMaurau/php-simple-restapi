<?php

/**
 * ------------------------------------------------------------------------------
 * You can put anything you want here that you need to have access to globally.
 * This is mostly for configuration purposes.
 * ------------------------------------------------------------------------------

 */
// ------------------------------------------------------------------------------
// Define the directory of the index.php for route handling.
// This will result into https://domain.com/{API_ROOT}
// ------------------------------------------------------------------------------
define('API_ROOT', 'php-simple-restapi/');

// Set the secret passphrase used for the JWT (JSON Web Token) encryption.
// ------------------------------------------------------------------------------
define('JWT_SECRET', '3-V{ii7y/|PNo~pU571q#ASJwA%9Csq2+3Ezj7-];<Q!&m}Wl[VDXGwPi^2!T#OV');

// Set the general salt that will be used for the password hashing (SHA256)
// ------------------------------------------------------------------------------
define('PASSWORD_SALT', '4vWdhVs>O_UtX+|c6v+6[RrhK+tN[;@oAji:0%VBsV$iy*#jwmb|T+kaA.<c75.:');

// ------------------------------------------------------------------------------
// DB Credentials
// ------------------------------------------------------------------------------
// Recommended to use the IP address instead of localhost. Localhost could
// (depending on the machine running it) result in socket-connection errors.
// ------------------------------------------------------------------------------
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mydatabase');
// Set this if you'd like to have a Databse table Prefix
define('DB_PREFIX', '');
// The global charset for the database
define('DB_CHARSET', 'utf8mb4');

// ------------------------------------------------------------------------------
// Domains
// ------------------------------------------------------------------------------
// List the domains and their environment to run on
// ------------------------------------------------------------------------------
define('ENV_LOCAL', 'localhost');
define('ENV_DEVELOPMENT', 'dev.domain.com');
define('ENV_PRODUCTION', 'domain.com');

// ------------------------------------------------------------------------------
// Extra's
// ------------------------------------------------------------------------------
// Enable the option to allow the Token to be a GET-param in the URL.
// Warning: This Token will then be publically visible within the URL.
define('ALLOW_URL_GET_TOKEN', true);
// If you have enabled the above, Specify for which param to look for.
define('GET_TOKEN_PARAM', 'token');
