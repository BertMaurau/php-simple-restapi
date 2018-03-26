<?php

header('Content-Type: text/plain');

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/Migration.php';

// parse argv (command line params)
try {
    global $argv;
    parse_str($argv[1], $params);
    $direction = (isset($params['direction'])) ? $params['direction'] : ((isset($_GET['direction'])) ? $_GET['direction'] : 'up');
} catch (Exception $ex) {
    // do nothing
}

// get the migrations
$migrations = glob("mig*.php");
foreach ($migrations as $migration) {
    // include the class
    include $migration;

    // get the name and init the class
    $name = pathinfo($migration)['filename'];
    $migration = new $name;

    // start the migration
    echo "------------------------------------------------------------" . PHP_EOL;
    echo " Starting Migration: " . $migration -> getName() . " (" . $direction . ")" . PHP_EOL;
    echo " Version: " . $migration -> getVersion() . PHP_EOL;
    echo " Description: " . $migration -> getDescription() . PHP_EOL;
    echo " Checking previous run: ";

    $result = null;
    try {
        $result = $migration -> getMigrationByVersion($migration -> getVersion());
    } catch (Exception $ex) {
        echo $ex -> getMessage() . PHP_EOL;
    }

    // check if it has been previously executed and if the direction was the same
    if ($result && $result -> getDirection() === $direction) {
        echo "Already executed on " . $result -> getMigrated_on() . PHP_EOL;
    } else {

        // check if the direction is a callable function
        if (!is_callable(array($migration, $direction))) {
            echo "  Unknown migration direction: " . $direction . PHP_EOL;
            continue;
        }

        echo "No previous run found" . PHP_EOL;

        // start direction
        call_user_func(array($migration, $direction));

        // insert into the logging table
        $query = "INSERT INTO `" . DB_PREFIX . "migrations` (`version`, `name`, `description`, `migrated_on`, `direction`)"
                . " VALUES ("
                . "'" . DB::escape($migration -> getVersion()) . "',"
                . "'" . DB::escape($migration -> getName()) . "',"
                . "'" . DB::escape($migration -> getDescription()) . "',"
                . "CURRENT_TIMESTAMP,"
                . "'" . DB::escape($direction) . "');";
        try {
            DB::query($query);
        } catch (Exception $ex) {
            echo "   " . $ex -> getMessage() . PHP_EOL;
        }
    }

    echo " Ended Migration: " . $migration -> getName() . PHP_EOL;
}