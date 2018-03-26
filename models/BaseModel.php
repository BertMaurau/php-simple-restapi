<?php

/**
 * Description of BaseModel
 *
 * Handle every 'general' function that a Model that extends this class could use.
 *
 * - validate       Validate the values against the define rules
 * - map            Map the values to the model-properties
 * - addAttrbute    Add non-property values to the model as an attribute
 * - getById        Get the resource by the given ID
 * - findBy         Find a model by the given property
 * - getAll         Get all resources
 * - insert         Insert/create a new resource
 * - update         Update an existing resource
 * - delete         Delete an existing resource
 *
 * @author Bert Maurau
 */
class BaseModel
{

    // |------------------------------------------------------------------------
    // |  Model Configuration
    // |------------------------------------------------------------------------
    // Reference to the Database table (gets set within the Model class)
    const DB_TABLE = "";
    // Define what is the primary key
    const PRIMARY_KEY = "id";
    // Allowed filter params for the get requests
    // Define on which fields the user can filter using GET params.
    const FILTERS = [];
    // Does the table have timestamps?
    // (created_at, updated_at, deleted_at)
    const TIMESTAMPS = false;
    // Use soft deletes?
    // (prevent actual record deletions, just update the deleted_at timestamp)
    const SOFT_DELETES = true;
    // Validation rules 'property' => [required, varType, min(length), max(length)]
    // (Ex. 'name' => [true, 'string', 1, 120])
    const VALIDATION = [];

    // |------------------------------------------------------------------------
    // |  Properties
    // |------------------------------------------------------------------------
    // holds extra attributes that are not model-properties
    // items that don't have any setters
    public $attributes = array();

    // |------------------------------------------------------------------------
    // |  Model Functions
    // |------------------------------------------------------------------------
    /**
     * Validate the current model's properties using the validation rules
     * @return array valid|reason
     */
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
            //
            // check if property has a specific rule defined
            if (isset(static::VALIDATION[$property])) {

                // check first if it's a required property
                $reqRequired = static::VALIDATION[$property][0];
                if (!$value && $reqRequired) {
                    return [false, "Missing required property " . $property];
                }

                // check for the variable type
                $reqVarType = static::VALIDATION[$property][1];
                $parVarType = gettype($value);
                if ($parVarType != $reqVarType) {
                    return [false, "Expected `" . $reqVarType . "`, got `" . $parVarType . "` for " . $property];
                }

                // if the property should be an email, do the necessary validations
                if ($reqVarType === 'email') {
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
     * Map the given properties to self, calling the setters.
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
                . "FROM " . DB_PREFIX . static::DB_TABLE . " "
                . "WHERE `" . static::PRIMARY_KEY . "` = " . DB::escape($id) . " "
                . ((static::SOFT_DELETES) ? " AND " . DB_PREFIX . static::DB_TABLE . ".deleted_at IS NULL" : "")
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
     * @param array $fieldsWithValues
     * @param integer $take
     * @param integer $skip
     * @return $this
     */
    public function findBy($fieldsWithValues = array(), $take = 120, $skip = 0)
    {
        // check if the requested field exists for this model
        foreach ($fieldsWithValues as $field => $value) {
            if (!in_array($field, get_object_vars($this))) {
                throw new Exception("`" . $field . "` is not a recognized property.");
            } else {
                $conditions[] = "`" . $field . "` = '" . DB::escape($value) . "'";
            }
        }

        $query = " SELECT * "
                . "FROM " . DB_PREFIX . static::DB_TABLE . " "
                . "WHERE " . ((count($conditions)) ? implode(' AND ', $conditions) : "") . " "
                . ((static::SOFT_DELETES) ? " AND " . DB_PREFIX . static::DB_TABLE . ".deleted_at IS NULL" : "")
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
            // check if the requested filter is allowed or available.
            if (in_array($field, static::FILTERS)) {
                $conditions[] = "`$field` LIKE '%$value%'";
            }
        }

        // Pagination
        $take = (isset($filters['take']) && is_int($filters['take'])) ? $filters['take'] : 100;
        $skip = (isset($filters['skip']) && is_int($filters['skip'])) ? $filters['skip'] : 0;

        $response = [];
        $query = " SELECT * "
                . "FROM " . DB_PREFIX . static::DB_TABLE . " "
                . "WHERE " . ((count($conditions)) ? implode(' AND ', $conditions) : "") . " "
                . ((static::SOFT_DELETES) ? " AND " . DB_PREFIX . static::DB_TABLE . ".deleted_at IS NULL" : "")
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
        // This should be modified to be a bit more secure, but normally public
        // properties will be filtered out, as well as the attributes property.
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'attributes' && !empty($value) && is_callable(array($this, 'get' . ucfirst($key)))) {
                $keys[] = '`' . DB::escape($key) . '`';
                $values[] = DB::escape($value);
            }
        }

        // Do more checks here for security..

        $query = " INSERT "
                . "INTO " . DB_PREFIX . static::DB_TABLE . "(" . implode(",", $keys) . ") "
                . "VALUES ('" . implode("','", $values) . "');";

        // replace nulls with real nulls (for ex. deleted_at)
        $query = str_replace("'(null)'", "NULL", $query);

        $result = DB::query($query);

        // Get the ID and add it to the model response
        $this -> id = DB::getId();

        return $this;
    }

    /**
     * Update Model
     * @return $this
     */
    public function update()
    {
        // This should be modified to be a bit more secure, but normally public
        // properties will be filtered out, as well as the attributes property.
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'attributes' && !empty($value) && is_callable(array($this, 'get' . ucfirst($key)))) {
                $update[] = '`' . DB::escape($key) . '`' . " = '" . DB::escape($value) . "'";
            }
        }

        $query = " UPDATE " . DB_PREFIX . static::DB_TABLE . " "
                . "SET " . implode(",", $update) . " "
                . "WHERE `" . static::PRIMARY_KEY . "` = " . DB::escape($this -> getId()) . ";";

        // replace nulls with real nulls (for ex. deleted_at)
        $query = str_replace("'(null)'", "NULL", $query);

        $result = DB::query($query);

        return $this;
    }

    /**
     * Delete Model
     * $param boolean force hard-delete
     * @return $this
     */
    public function delete($hardDelete = false)
    {
        if (ALLOW_FORCE_DELETES && ($hardDelete || !static::SOFT_DELETES)) {
            $query = " DELETE FROM " . DB_PREFIX . static::DB_TABLE . " "
                    . "WHERE `" . static::PRIMARY_KEY . "` = " . DB::escape($this -> getId()) . ";";
            $result = DB::query($query);
        } else {
            // update the timestamp
            $this -> setDeleted_at(date('Y-m-d H:i:s')) -> update();
        }

        return $this;
    }

}
