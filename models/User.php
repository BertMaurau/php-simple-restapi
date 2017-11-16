<?php

/**
 * Description of User
 *
 * @author Bert Maurau
 */
class User extends BaseModel
{

    // Reference to the Database table
    const DB_TABLE = "users";
    // Allowed filter params for the get requests
    const FILTERS = ['firstname', 'lastname', 'email'];

    // Properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    protected $password;

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
        } else {
            return $this -> createObjectFromProperties($result -> fetch_assoc());
        }
    }

    public function getId()
    {
        return $this -> id;
    }

    public function getFirstname()
    {
        return $this -> firstname;
    }

    public function getLastname()
    {
        return $this -> lastname;
    }

    public function getEmail()
    {
        return $this -> email;
    }

    public function getPassword()
    {
        return $this -> password;
    }

    public function setId($id)
    {
        $this -> id = $id;
        return $this;
    }

    public function setFirstname($firstname)
    {
        $this -> firstname = $firstname;
        return $this;
    }

    public function setLastname($lastname)
    {
        $this -> lastname = $lastname;
        return $this;
    }

    public function setEmail($email)
    {
        $this -> email = $email;
        return $this;
    }

    public function setPassword($password)
    {
        $this -> password = hash('sha256', $password . Constants::PASSWORD_SALT);
        return $this;
    }

}
