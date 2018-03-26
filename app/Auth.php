<?php

/**
 * Description of Auth
 *
 * Handles everything concerning the Authentication and user sessions etc.
 *
 * @author Bert Maurau
 */
class Auth
{

    // Put auth items here for easier access
    // Don't forget to add the necessary getter/setter.
    private static $userId;

    /**
     * Load Auth values
     * @param array $properties
     */
    public function loadAuth($properties = null)
    {
        foreach ($properties as $key => $value) {
            if (isset($properties)) {
                // loop properties and attempt to call the setter
                foreach ($properties as $key => $value) {
                    $setter = 'set' . ucfirst($key);
                    // check if the setter exists and is callable
                    if (is_callable(array($this, $setter))) {
                        // execute the setter
                        call_user_func(array($this, $setter), $value);
                    }
                }
            }
        }
    }

    /**
     * Get the UserID
     * @return integer
     */
    static function getUserId()
    {
        return self::$userId;
    }

    /**
     * Set the UserID
     * @param integer $userId
     */
    static function setUserId($userId)
    {
        self::$userId = $userId;
    }

}
