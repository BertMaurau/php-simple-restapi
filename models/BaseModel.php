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
    // Use soft deletes?
    const SOFT_DELETES = true;
    // Validation rules
    const VALIDATION = [];

    // |------------------------------------------------------------------------
    // |  Properties
    // |------------------------------------------------------------------------
    // holds extra attributes that are not model-properties
    public $attributes = array();

    // |------------------------------------------------------------------------
    // |  Model Functions
    // |------------------------------------------------------------------------
    public function validate()
    {
        // check if there are validation rules
        if (count(static::VALIDATION) < 1) {
            // if not, just return valid
            return [true, 'OK'];
        }

        // get object properties
        $properties = get_object_vars($this);
        foreach ($properties as $property => $value) {
            // check with validation rule
            // check if property has a rule
            if (isset(static::VALIDATION[$property])) {

                // check if required
                $reqRequired = static::VALIDATION[$property][0];
                if (!$value && $reqRequired) {
                    return [false, "Missing required property " . $property];
                }

                // check var type
                $reqVarType = static::VALIDATION[$property][1];
                $parVarType = gettype($value);
                if ($parVarType != $reqVarType) {
                    return [false, "Expected `" . $reqVarType . "`, got `" . $parVarType . "` for " . $property];
                }

                if ($reqVarType == 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        return [false, $value . " is not a valid email address."];
                    }
                } else {
                    // check min length or value (only strings and integers)
                    $reqMin = static::VALIDATION[$property][2];
                    $reqMax = static::VALIDATION[$property][3];

                    if ($reqVarType == 'string') {
                        $parLength = strlen($value);
                        if ($parLength < $reqMin) {
                            return [false, $property . " requires a min-length of " . $reqMin . ". " . $parLength . " given."];
                        }
                        if ($parLength > $reqMax) {
                            return [false, $property . " requires a max-length of " . $reqMax . ". " . $parLength . " given."];
                        }
                    }
                    // check max length or value
                    if ($reqVarType == 'integer') {

                        if ($value < $reqMin) {
                            return [false, $property . " has a min-value of " . $reqMin . ". " . $parLength . " given."];
                        }
                        if ($value > $reqMax) {
                            return [false, $property . " has a max-value of " . $reqMax . ". " . $parLength . " given."];
                        }
                    }
                }
            }
        }

        // if all passed..
        return [true, 'OK'];
    }

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
        $query = " SELECT * "
                . "FROM " . static::DB_TABLE . " "
                . "WHERE id = $id "
                . ((static::SOFT_DELETES) ? " AND (" . static::DB_TABLE . ".deleted_at IS NULL OR " . static::DB_TABLE . ".deleted_at = '0000-00-00 00:00:00')" : "")
                . "LIMIT 1;";
        $result = DB::query($query);
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
    public function findBy($field, $value, $take = 120, $skip = 0)
    {
        $query = " SELECT * "
                . "FROM " . static::DB_TABLE . " "
                . "WHERE $field = '" . DB::escape($value) . "' "
                . ((static::SOFT_DELETES) ? " AND (" . static::DB_TABLE . ".deleted_at IS NULL OR " . static::DB_TABLE . ".deleted_at = '0000-00-00 00:00:00')" : "")
                . "LIMIT $take OFFSET $skip;";
        $result = DB::query($query);
        if ($take && $take === 1) {
            if ($result -> num_rows < 1) {
                return false;
            } else {
                return $this -> map($result -> fetch_assoc());
            }
        } else {
            $response = [];
            while ($row = $result -> fetch_assoc()) {
                $response[] = (new $this) -> map($row);
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

        // Pagination
        $take = (isset($filters['take']) && is_int($filters['take'])) ? $filters['take'] : 100;
        $skip = (isset($filters['skip']) && is_int($filters['skip'])) ? $filters['skip'] : 0;

        $response = [];
        $query = " SELECT * "
                . "FROM " . static::DB_TABLE . " "
                . "WHERE " . ((count($conditions)) ? implode(' AND ', $conditions) : "") . " "
                . ((static::SOFT_DELETES) ? " AND (" . static::DB_TABLE . ".deleted_at IS NULL OR " . static::DB_TABLE . ".deleted_at = '0000-00-00 00:00:00')" : "")
                . "LIMIT $take OFFSET $skip;";
        $result = DB::query($query);
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
            if ($key !== 'attributes') {
                $keys[] = '`' . $key . '`';
                $values[] = DB::escape($value);
            }
        }

        // Do checks here for security..

        $query = " INSERT "
                . "INTO " . static::DB_TABLE . "(" . implode(",", $keys) . ") "
                . "VALUES ('" . implode("','", $values) . "');";
        $result = DB::query($query);

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
            if ($key !== 'attributes') {
                $update[] = '`' . $key . '`' . " = '" . DB::escape($value) . "'";
            }
        }

        $query = " UPDATE " . static::DB_TABLE . " "
                . "SET " . implode(",", $update) . " "
                . "WHERE id = " . $this -> getId() . ";";
        $result = DB::query($query);

        return $this;
    }

    /**
     * Delete Model
     * @return $this
     */
    public function delete($hardDelete = false)
    {
        if ($hardDelete || !static::SOFT_DELETES) {
            $query = " DELETE FROM " . static::DB_TABLE . " "
                    . "WHERE id = " . $this -> getId() . ";";
            $result = DB::query($query);
        } else {
            $this -> setDeleted_at(date('Y-m-d H:i:s')) -> update();
        }

        return $this;
    }

}
