<?php

/**
 * Migration 001
 * This will be used for the initial setup of the Database
 */
class mig001_init extends Migration
{

    public function __construct()
    {
        /**
         * General info about the migration
         */
        $this -> setVersion('0.0.1');
        $this -> setName('mig001_init'); // must be the same as the classname
        $this -> setDescription('The initial creation of the database structure for version v1.3.1.');
        $this -> setStopOnFatal(false);

        // Don't try to connect with the DB, because it might not even exist yet.
        DB::init($connectWithDb = false);
    }

    /**
     * Define what to do when migrating up (default)
     */
    public function up()
    {

        // First, create the database if it doesn't exist already
        $sql = "CREATE DATABASE IF NOT EXISTS `" . DB::getDatabase() . "` CHARACTER SET " . DB_CHARSET . " COLLATE " . DB_COLLATE . ";";
        try {
            DB::query($sql);
            echo "   Database is present: `" . DB::getDatabase() . "`" . PHP_EOL;
        } catch (Exception $ex) {
            echo "   " . $ex -> getMessage() . PHP_EOL;
            exit();
        }

        // Close the current connection and reconnect, but this time with the DB
        DB::close();
        DB::init($connectWithDb = true);

        // create the migrations table
        $sql = "CREATE TABLE `" . DB_PREFIX . "migrations` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `version` varchar(5) DEFAULT NULL,
                    `name` varchar(32) DEFAULT NULL,
                    `description` varchar(255) DEFAULT NULL,
                    `migrated_on` datetime DEFAULT NULL,
                    `direction` varchar(12) DEFAULT NULL,
                    `deleted_at` datetime DEFAULT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                  )";
        try {
            DB::query($sql);
            echo "   Created Table: `" . DB_PREFIX . "migrations`" . PHP_EOL;
        } catch (Exception $ex) {
            echo "   " . $ex -> getMessage() . PHP_EOL;
            if ($this -> getStopOnFatal()) {
                // end the script if migrations are fatal
                die();
            }
        }

        // create the users table
        $sql = "CREATE TABLE `" . DB_PREFIX . "users` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `firstname` varchar(64) DEFAULT NULL,
                    `lastname` varchar(64) DEFAULT NULL,
                    `email` varchar(128) DEFAULT NULL,
                    `deleted_at` datetime DEFAULT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email` (`email`)
                  )";
        try {
            DB::query($sql);
            echo "   Created Table: `" . DB_PREFIX . "users`" . PHP_EOL;
        } catch (Exception $ex) {
            echo "   " . $ex -> getMessage() . PHP_EOL;
            if ($this -> getStopOnFatal()) {
                // end the script if migrations are fatal
                die();
            }
        }
    }

    /**
     * Define what to do when migrating down (revert)
     */
    public function down()
    {
        DB::init($connectWithDb = false);

        $sql = "DROP DATABASE IF EXISTS `" . DB::getDatabase() . "`;";
        try {
            DB::query($sql);
            echo "   Dropped Datbase: `" . DB::getDatabase() . "`" . PHP_EOL;
        } catch (Exception $ex) {
            echo "   " . $ex -> getMessage() . PHP_EOL;
            if ($this -> getStopOnFatal()) {
                // end the script if migrations are fatal
                die();
            }
        }
    }

}

?>