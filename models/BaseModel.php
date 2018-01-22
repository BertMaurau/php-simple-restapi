<?php

/**
 * Description of BaseModel
 *
 * @author Bert Maurau
 */
class BaseModel
{

    // |------------------------------------------------------------------------
    // |  Model Configuration
    // |------------------------------------------------------------------------
    // Reference to the Database table
    const DB_TABLE = "";
    // Allowed filter params for the get requests
    const FILTERS = [];
    // Does the table have timestamps?
    const TIMESTAMPS = false;

    // |------------------------------------------------------------------------
    // |  Properties
    // |------------------------------------------------------------------------
    // holds extra attributes that are not model-properties
    public $attributes = array();

    // |------------------------------------------------------------------------
    // |  Model Functions
    // |------------------------------------------------------------------------
    /**
     * Map the given properties to self
     * @param object $properties
     * @return $this
     */
    public function map($properties = null)
    {
        if (isset($properties)) {
            // loop properties and attempt to call the setter
            foreach ($properties as $key => $value) {
                $setter = 'set' . ucfirst($key);
                // check if the setter exists and is callable
                if (is_callable(array($this, $setter))) {
                    // execute the setter
                    call_user_func(array($this, $setter), $value);
                } else {
                    // not a property, add to the attributes list
                    $this -> addAttribute($key, $value);
                }
            }
            return $this;
        }
    }

    /**
     * Add item as attribute
     * @param string $property
     * @param any $value
     * @return $this
     */
    public function addAttribute($property, $value)
    {
        $this -> attributes[$property] = $value;
        return $this;
    }

    /**
     * Get model by ID
     * @param integer $id
     * @return $this
     */
    public function getById($id)
    {
        $result = DB::query("SELECT * FROM " . static::DB_TABLE . " WHERE id = $id LIMIT 1;");
        if ($result -> num_rows < 1) {
            return false;
        } else {
            // Create an object from the result
            return $this -> map($result -> fetch_assoc());
        }
    }

    /**
     * Get model by specific field
     * @param string $field
     * @param type $value
     * @return $this
     */
    public function findBy($field, $value, $limit = null)
    {
        $result = DB::query("SELECT * FROM " . static::DB_TABLE . " WHERE $field = '" . DB::escape($value) . "' " . (($limit) ? "LIMIT $limit" : "") . ";");
        if ($limit && $limit === 1) {
            if ($result -> num_rows < 1) {
                return false;
            } else {
                return $this -> map($result -> fetch_assoc());
            }
        } else {
            $response = [];
            while ($row = $result -> fetch_assoc()) {
                $response[] = new $this -> createObjectFromProperties($row);
            }
            return $response;
        }
    }

    /**
     * Get all Models
     * @param array $filter
     * @return array
     */
    public function getAll($filter = [])
    {
        // Build WHERE conditions
        $conditions = array();
        foreach ($filter as $field => $value) {
            if (in_array($field, static::FILTERS)) {
                $conditions[] = "`$field` LIKE '%$value%'";
            }
        }
        $response = [];
        $result = DB::query("SELECT * FROM " . static::DB_TABLE . " " . ((count($conditions)) ? "WHERE " . implode(' AND ', $conditions) : "") . ";");
        while ($row = $result -> fetch_assoc()) {
            $response[] = (new $this) -> map($row);
        }
        return $response;
    }

    /**
     * Insert Model
     * @return $this
     */
    public function insert()
    {
        // This should be modified, write it more securly to prevent from trying to insert public properties
        foreach ($this as $key => $value) {
            $keys[] = '`' . $key . '`';
            $values[] = DB::escape($value);
        }

        // Do checks here for security..

        $sql = "INSERT INTO " . static::DB_TABLE . "(" . implode(",", $keys) . ") VALUES ('" . implode("','", $values) . "');";
        $result = DB::query($sql);

        // Get the ID
        $this -> id = DB::getId();

        return $this;
    }

    /**
     * Update Model
     * @return $this
     */
    public function update()
    {
        // This should be modified, write it more securly to prevent from trying to insert public properties
        foreach ($this as $key => $value) {
            $update[] = $key . " = '" . DB::escape($value) . "'";
        }

        $sql = "UPDATE " . static::DB_TABLE . " SET " . implode(",", $update) . " WHERE id = " . $this -> getId() . ";";
        $result = DB::query($sql);

        return $this;
    }

    /**
     * Delete Model
     * @return $this
     */
    public function delete()
    {
        $sql = "DELETE FROM " . static::DB_TABLE . " WHERE id = " . $this -> getId() . ";";
        $result = DB::query($sql);

        return $this;
    }

}
