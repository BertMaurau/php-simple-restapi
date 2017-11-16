<?php

/**
 * Description of connection
 *
 * @author Bert Maurau
 */
class DB
{

    public static $mysqli;

    static function init()
    {
        self::$mysqli = new mysqli(Constants::DB_HOST, Constants::DB_USER, Constants::DB_PASS, Constants::DB_NAME);
    }

    public static function escape($value)
    {
        return self::$mysqli -> real_escape_string($value);
    }

    public static function getId()
    {
        return self::$mysqli -> insert_id;
    }

    public static function getAffectedRows()
    {
        return self::$mysqli -> affected_rows;
    }

    public static function query($sql)
    {
        $result = self::$mysqli -> query($sql);
        return $result;
    }

}
