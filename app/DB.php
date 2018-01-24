<?php

/**
 * Description of connection
 *
 * @author Bert Maurau
 */
class DB
{

    // holds the connection with the database
    public static $mysqli;

    /**
     * Init a new connection
     * @throws Exception
     */
    static function init()
    {
        self::$mysqli = new mysqli(Constants::DB_HOST, Constants::DB_USER, Constants::DB_PASS, Constants::DB_NAME);
        if (!self::$mysqli) {
            throw new Exception(self::getLastError());
        }
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
     * Execute a given query
     * @param string $sql
     * @return resultset
     */
    public static function query($sql)
    {
        if (!$result = self::$mysqli -> query($sql)) {
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
     * Return the last mysql error
     * @return string
     */
    public static function getLastError()
    {
        return self::$mysqli -> error;
    }

}
