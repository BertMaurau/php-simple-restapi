<?php

/**
 * Description of Env
 *
 * Handles the current environment. You can modify the checks to your own
 * needs. Currently this just checks the domain it's running on.
 *
 * @author Bert Maurau
 */
class Env
{

    const ENV_LOCALHOST = "localhost";
    const ENV_DEVELOPMENT = "development";
    const ENV_PRODUCTION = "production";

    /**
     * Get the current ENV
     * @global array $argv
     * @return string
     */
    public static function getEnv()
    {
        // check if the script got executed via a browser
        if (isset($_SERVER['HTTP_HOST'])) {
            // get the current host
            $host = $_SERVER['HTTP_HOST'];
        } else {
            // check for any given command line parameters.
            global $argv;
            parse_str($argv[1], $params);
            // get the current host
            $host = $params['ENV'];
        }

        // check the resulted host and set the current env
        switch ($host) {

            case ENV_LOCAL:
                return self::ENV_LOCALHOST;
                break;
            case ENV_DEVELOPMENT:
                return self::ENV_DEVELOPMENT;
                break;
            case ENV_PRODUCTION:
                return self::ENV_PRODUCTION;
                break;
        }
    }

}
