<?php

/**
 * Description of User
 *
 * @author Bert Maurau
 */
class User extends BaseModel
{

    // |------------------------------------------------------------------------
    // |  Model Configuration
    // |------------------------------------------------------------------------
    // Reference to the Database table
    const DB_TABLE = "users";
    // Allowed filter params for the get requests
    const FILTERS = ['firstname', 'lastname', 'email'];
    // Does the table have timestamps?
    const TIMESTAMPS = true;

    // |------------------------------------------------------------------------
    // |  Properties
    // |------------------------------------------------------------------------
    // integer
    public $id;
    // string
    public $firstname;
    // string
    public $lastname;
    // string
    public $email;
    // string
    protected $password;

    // |------------------------------------------------------------------------
    // |  Model Functions
    // |------------------------------------------------------------------------
    /**
     * Check for a valid login
     * @param string $email
     * @param string $password
     * @return User
     */
    public function validateLogin($email, $password)
    {
        $result = DB::query("SELECT * FROM " . static::DB_TABLE . " WHERE email = '" . DB::escape($email) . "' AND password = '" . DB::escape($password) . "';");
        if ($result -> num_rows < 1) {
            return null;
        } else if ($result -> num_rows > 1) {
            throw new Exception("Multiple logins found. Conflict!");
        } else {
            return $this -> map($result -> fetch_assoc());
        }
    }

    // |------------------------------------------------------------------------
    // |  Getters
    // |------------------------------------------------------------------------
    /**
     * Get ID
     * @return integer
     */
    public function getId()
    {
        return $this -> id;
    }

    /**
     * Get FirstName
     * @return string
     */
    public function getFirstname()
    {
        return $this -> firstname;
    }

    /**
     * Get LastName
     * @return string
     */
    public function getLastname()
    {
        return $this -> lastname;
    }

    /**
     * Get Email
     * @return string
     */
    public function getEmail()
    {
        return $this -> email;
    }

    /**
     * Get Password
     * @return string
     */
    public function getPassword()
    {
        return $this -> password;
    }

    // |------------------------------------------------------------------------
    // |  Setters
    // |------------------------------------------------------------------------
    /**
     * Set ID
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this -> id = (int) $id;
        return $this;
    }

    /**
     * Set FirstName
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this -> firstname = (string) $firstname;
        return $this;
    }

    /**
     * Set LastName
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this -> lastname = (string) $lastname;
        return $this;
    }

    /**
     * Set Email
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this -> email = (string) $email;
        return $this;
    }

    /**
     * Set Password
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this -> password = (string) hash('sha256', $password . Constants::PASSWORD_SALT);
        return $this;
    }

}
