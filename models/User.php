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
    // Define what is the primary key
    const PRIMARY_KEY = "id";
    // Allowed filter params for the get requests
    const FILTERS = ['firstname', 'lastname', 'email'];
    // Does the table have timestamps? (created_at, updated_at, deleted_at)
    const TIMESTAMPS = true;
    // Use soft deletes?
    const SOFT_DELETES = true;
    // Validation rules
    const VALIDATION = [
        'firstname' => [true, 'string', 1, 128],
        'lastname'  => [true, 'string', 1, 128],
        'email'     => [true, 'email']
    ];

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
    // string
    public $created_at;
    // string
    public $updated_at;
    // string
    protected $deleted_at;

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
        // do not forget to call your setter first to do any manipulations
        // like hashing the password..
        $this
                -> setEmail($email)
                -> setPassword($password);

        $user = $this -> findBy(array(
            'email'    => $this -> getEmail(),
            'password' => $this -> getPassword()), 1);
        if (!$user) {
            return null;
        } else {
            return $user;
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

    public function getCreated_at()
    {
        return $this -> created_at;
    }

    public function getUpdated_at()
    {
        return $this -> updated_at;
    }

    public function getDeleted_at()
    {
        return $this -> deleted_at;
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
        $this -> password = (string) hash('sha256', $password . PASSWORD_SALT);
        return $this;
    }

    /**
     * Set Created At
     * @param string $created_at
     * @return $this
     */
    public function setCreated_at($created_at)
    {
        $this -> created_at = $created_at;
        return $this;
    }

    /**
     * Set Updated At
     * @param string $updated_at
     * @return $this
     */
    public function setUpdated_at($updated_at)
    {
        $this -> updated_at = $updated_at;
        return $this;
    }

    /**
     * Set Deleted At
     * @param string $deleted_at
     * @return $this
     */
    public function setDeleted_at($deleted_at)
    {
        $this -> deleted_at = $deleted_at;
        return $this;
    }

}
