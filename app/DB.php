<?php

/**
 * Description of DB
 *
 * This handles everything concerning the Database connection.
 *
 * @author Bert Maurau
 */
class DB
{

    // holds the connection with the database
    private static $mysqli;
    private static $database;
    private static $host;
    private static $user;
    private static $password;

    /**
     * Init a new connection depending on the current environment.
     * The environments can be defined within the app/Env file.
     * @throws Exception
     */
    static function init($connectWithDatabase = true)
    {
        switch (Env::getEnv()) {

            case Env::ENV_LOCALHOST:
                self::$database = DB_LOCAL_NAME;
                self::$user = DB_LOCAL_USER;
                self::$password = DB_LOCAL_PASS;
                self::$host = DB_LOCAL_HOST;
                break;
            case Env::ENV_DEVELOPMENT:
                self::$database = DB_DEV_NAME;
                self::$user = DB_DEV_USER;
                self::$password = DB_DEV_PASS;
                self::$host = DB_DEV_HOST;
                break;
            case Env::ENV_PRODUCTION:
                self::$database = DB_PROD_NAME;
                self::$user = DB_PROD_USER;
                self::$password = DB_PROD_PASS;
                self::$host = DB_PROD_HOST;
                break;
            /*
             * Add as many environment connects as you want. As long as you have
             * the required database credentials configured.
             */
            default:
                break;
        }

        // connect with the database
        if (!self::$mysqli = new mysqli(self::$host, self::$user, self::$password, ($connectWithDatabase) ? self::$database : null)) {
            throw new Exception("Failed to connect with the Database.");
        }


        // Set the charset to allow for example emoticons
        self::$mysqli -> set_charset(DB_CHARSET);
    }

    /**
     * Escape the given value
     * @param any $value
     * @return any
     */
    public static function escape($value)
    {
        return self::$mysqli -> real_escape_string($value);
    }

    /**
     * Get the last inserted ID
     * @return integer
     */
    public static function getId()
    {
        return self::$mysqli -> insert_id;
    }

    /**
     * Get the amount of affected rows
     * @return integer
     */
    public static function getAffectedRows()
    {
        return self::$mysqli -> affected_rows;
    }

    /**
     * Execute the given query
     * @param string $query
     * @return resultset
     */
    public static function query($query)
    {
        if (!$result = self::$mysqli -> query($query)) {
            throw new Exception(self::getLastError());
        }
        return $result;
    }

    /**
     * Close the connection (if open)
     */
    public static function close()
    {
        if (self::$mysqli) {
            self::$mysqli -> close();
        }
    }

    /**
     * Return the last mysqli error
     * @return string
     */
    public static function getLastError()
    {
        return self::$mysqli -> error;
    }

    /**
     * Return the current database
     * @return string database name
     */
    public static function getDatabase()
    {
        return self::$database;
    }

}
