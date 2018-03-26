<?php

/**
 * Description of Migration
 *
 * Handles the execution of DB migrations/seedings
 *
 * @author Bert Maurau
 */
class Migration extends BaseModel
{

    // define if the migration should fail on any error
    protected $stopOnFatal = false;
    public $id;
    public $version;
    public $name;
    public $description;
    public $migrated_on;
    public $direction;
    protected $deleted_at;
    protected $created_at;
    protected $updated_at;

    /**
     * Get a migration by a specific version
     *
     * @param string $version
     * @return this
     */
    public function getMigrationByVersion($version)
    {
        $query = " SELECT * "
                . "FROM " . DB::getDatabase() . ".`" . DB_PREFIX . "migrations` "
                . "WHERE `version` = '" . DB::escape($version) . "' "
                . "LIMIT 1;";

        $result = DB::query($query);
        if ($result -> num_rows < 1) {
            return false;
        } else {
            // Create an object from the result
            return $this -> map($result -> fetch_assoc());
        }
    }

    public function getStopOnFatal()
    {
        return $this -> stopOnFatal;
    }

    public function getId()
    {
        return $this -> id;
    }

    public function getVersion()
    {
        return $this -> version;
    }

    public function getName()
    {
        return $this -> name;
    }

    public function getDescription()
    {
        return $this -> description;
    }

    public function getMigrated_on()
    {
        return $this -> migrated_on;
    }

    public function getDirection()
    {
        return $this -> direction;
    }

    public function getDeleted_at()
    {
        return $this -> deleted_at;
    }

    public function getCreated_at()
    {
        return $this -> created_at;
    }

    public function getUpdated_at()
    {
        return $this -> updated_at;
    }

    public function setStopOnFatal($stopOnFatal)
    {
        $this -> stopOnFatal = $stopOnFatal;
        return $this;
    }

    public function setId($id)
    {
        $this -> id = (int) $id;
        return $this;
    }

    public function setVersion($version)
    {
        $this -> version = (string) $version;
        return $this;
    }

    public function setName($name)
    {
        $this -> name = (string) $name;
        return $this;
    }

    public function setDescription($description)
    {
        $this -> description = (string) $description;
        return $this;
    }

    public function setMigrated_on($migrated_on)
    {
        $this -> migrated_on = (string) $migrated_on;
        return $this;
    }

    public function setDirection($direction)
    {
        $this -> direction = (string) $direction;
        return $this;
    }

    public function setDeleted_at($deleted_at)
    {
        $this -> deleted_at = (string) $deleted_at;
        return $this;
    }

    public function setCreated_at($created_at)
    {
        $this -> created_at = (string) $created_at;
        return $this;
    }

    public function setUpdated_at($updated_at)
    {
        $this -> updated_at = (string) $updated_at;
        return $this;
    }

}
