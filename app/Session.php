<?php

/**
 * Description of Session
 *
 * @author Bert Maurau
 */
class Session
{

    // put session items here
    private static $userId;

    /**
     * Load Session values
     * @param array $properties
     */
    public function loadSession($properties = null)
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
