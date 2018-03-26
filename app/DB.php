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

    /**
     * Init a new connection depending on the current environment.
     * The environments can be defined within the app/Env file.
     * @throws Exception
     */
    static function init($connectWithDatabase = true)
    {
        switch (Env::getEnv()) {

            case Env::ENV_LOCALHOST:
                self::$database = ($connectWithDatabase) ? DB_LOCAL_NAME : null;
                self::$mysqli = new mysqli(DB_LOCAL_HOST, DB_LOCAL_USER, DB_LOCAL_PASS, self::$database);
                break;
            case Env::ENV_DEVELOPMENT:
                self::$database = ($connectWithDatabase) ? DB_DEV_NAME : null;
                self::$mysqli = new mysqli(DB_DEV_HOST, DB_DEV_USER, DB_DEV_PASS, self::$database);
                break;
            case Env::ENV_PRODUCTION:
                self::$database = ($connectWithDatabase) ? DB_DEV_NAME : null;
                self::$mysqli = new mysqli(DB_PROD_HOST, DB_PROD_USER, DB_PROD_PASS, self::$database);
                break;
            /*
             * Add as many environment connects as you want. As long as you have
             * the required database credentials configured.
             */
            default:
                break;
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
