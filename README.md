# A Simple and Basic RESTful API Example

This is a plain and simple PHP Restful API-base without the overhead of existing frameworks, non-required functions,.. just the "core" for a basic and simple setup.

You can get quickly started without a lot of configuration or the need to write a lot of code (unless it has to be for specific functionalities)

This project includes

    - Routing: Allowed endpoints with their matching HTTP verb.
    - Middleware: Gets called before dispatching the route. can be used for Authentication, logging,..
    - A MySQL Connection: Basic database connection.
    - Controllers: Handle the requests, do magic, ..
    - Models: Handle and manipulate the data, interact with the DB.
    - Environments: Load the Environment based on the Hostname.
    - Authentication: User authentication using the JWT module.
    = Migrations: Quickly write and execute your migrations (up/down).

## Installation

1. Clone this repository into your destination folder.

2. Download the required dependencies via Composer.
```
   $ composer install
```

3. Change the **/config/config.php** values to your liking.

(optional)
4. To get started with the provided example, you can run the initial migration via:
```
    Browser: /directory-of-the-api/migrations/run.php
```

## Required Dependencies (via Composer)

   - PHP >= 5.4.0
   - Route (https://github.com/thephpleague/route)
   - Zend Diactoros (https://github.com/zendframework/zend-diactoros)

## Modules Included

    - JWT (JSON Web Tokens) (https://jwt.io/)

## General directory structure

```
    - app                       Directory that contains the main App-like static classes.

      - Auth,php                Handles the load of the User values after passing the middleware.
      - DB.php                  Handles the main interaction with the MySQL database.
      - Env.php                 Gets used to determine the current running Environment.
      - Output.php              Handles the general output of the data with their respecting HTTP-codes.

    - config

      - config.php              Includes all the global configuration-values.

    - controllers

      - BaseController.php      Handles the basic request actions (index, show, create, update, delete).
      - UserController.php      Handles the User request actions (register, login) (Example).

    - crons                     Directory for your CRON-scripts.

    - middlewares

      - authentication.php      Contains the Authentication Middleware for Token-validation and such.

    - migrations                Directory for your database migrations.

      - Migration.php           The main migration-class to extend from.
      - mig001_init.php         The initial example migration.
      - run.php                 The migration "executer".

    - models

      - BaseModel.php           Handles the basics (insert, update, map, delete,..).
      - User.php                Handles login, register.

    - modules                   Directory for your "third-party" modules that aren't part of Composer.

      - JWT.php                 JWT (JSON Web Tokens) Class.

    - .htaccess                 Allow for pretty URLs, handle the Authentication Header.
    - composer.json             Package information.
    - index.php                 Brings everything together.
    - loader.php                Loads all the required files.
    - routes.php                The file that holds all the defined routes.
```

## Example Endpoints

```
    GET     /me     			Get the currently logged-in User (showing the Session part).
    GET     /users          		Get all Users ( allowed filters = ['firstname', 'lastname', 'email'] ex. /users?firstname=john ).
    GET     /users/{id}     		Get a specific User.
    POST    /users          		Create a new User.
    PATCH   /users/{id}     		Update an existing User.
    DELETE  /users/{id}     		Delete an existing User.
    POST    /users/validate_login	Validate the login based on post credentials.
```

## Middelwares

The JWT (JSON Web Tokens) module is used for this basic example.
You can use oAuth or whatever Authentication method you would like, but you'll need to add the logic and such yourself.

After passing the Middleware, the 'user_data' will be available in the **Auth** class and can be accessed like this:

```php
    Auth::getUserId();
```

You can add as much properties to the Auth class as you like and you can add them to the JWT encryption string for future access. (See the Auth part)

When the request doesn't get past the middleware, then it'll probably be because of a missing Token or an invalid Token.

Response when the Token is invalid or missing

```json
    {
        "code": 401,
        "message": "Not Authorized!"
    }
```

## Auth

I added a class called Auth.
This class can be used to share user/authentication stuff from the Middleware arround to the API for easy access.

**For example.** After logging in, you decide to keep track of the UserID, the Language, other stuff.. and pass these values via the JWT Token. You can then add properties to the Auth class (this has currently only the UserID). Then, when you need to access that value, you can simply call `Auth::get{Property}();` to access that Token-shared-value.

The middleware loads the Token-stored-properties via `(new Auth()) -> loadAuth($decoded_JWT_array);`

## Register a new Route

When you want to add a new route to your API you can just add the following line to the index file

```php
    $route -> map(<HTTP_VERB>, <URI>, [new <CONTROLLER>, <ACTION>]) -> middleware(<MIDDLEWARE>);
```

For Example
```php
    $route -> map('GET', '/users', [new UserController, 'index']) -> middleware($authentication);
```

Don't forget to define/create your controller to handle the requests actions. The 'index' refers to the function 'index' within the 'BaseController'.

If you'd like to use a custom function (for more validation of processing work) then you can simply define a new function within your controller and link that function to the route.

## BaseController

The BaseController already has the basic request actions, so that you can just extend this and set your model name.

```
  - index  [GET]        List all models.
  - show   [GET]        List a specific model.
  - create [POST]       Insert a new model.
  - update [PATCH]      Update a specific model.
  - delete [DELETE]     Delete a specific model.
```

### Create a new Controller

To create a new Controller (without extra functionalities) and use the basic endpoints, then you can simply add these lines to the class.


Example: NotesController

```php
class NotesController extends BaseController
{
    // Set the current ModelName
    const MODEL_NAME = "Note";
}
```

The only required item is the 'MODEL_NAME'. This defines which model to use when interacting with that controller (as the main model for the basic functions).

## BaseModel

The BaseModel already has the basic model actions, so that you can just extend this and quickly get started manipulating your data.

```
  - validate       Validate the values against the define model validation rules.
  - map            Map the values as model-properties by calling the setters.
  - addAttrbute    Add non-property values to the model as an attribute (gets called by the map() as well).
  - getById        Get the resource from the DB by the given ID.
  - findBy         Find a resource in the DB by the given properties and values.
  - getAll         Get all resources from the DB (for given $_GET parameters).
  - insert         Insert/create a new resource.
  - update         Update an existing resource.
  - delete         Delete an existing resource (soft or hard).
```

### Create a new Model

To create a new Model (without extra functionalities) and use the basic functions, then you can simply add these lines to the class.

Example Note

```php
class Note extends BaseModel
{
    // Reference to the Database table
    const DB_TABLE = "notes";
    // Allowed filter params for the get requests
    const FILTERS = ['note', 'user_id'];
    // Does the table have timestamps?
    const TIMESTAMPS = true;
    // Use soft deletes?
    const SOFT_DELETES = true;
    // Validation rules
    const VALIDATION = [
        'title'     => [true, 'string', 1, 128],
        'category'  => [false, 'integer', 0, 9]
    ];

    // example model-only function
    function..

        $resources = $this -> findBy(array('field' => value, 'field2' => value), $take = 5, $skip = 0);

        // do stuff with resources
        // these will already be mapped to objects via the BaseModel function 'findBy'.

        return $resources
;

    // integer
    public $id;
    // string
    public $note;
    // integer
    public $type;
    // string
    public $created_at;
    // string
    public $updated_at;
    // string
    protected $deleted_at;

    // GETTERS (required to be able to insert() and update() the model)
    // SETTERS (optional) BUT: If no setters are defined, all values will be put under the `attributes` property.
}
```

### Model Validation Rules

Each model can have its own validation rules that needs to be passed before it will be updated or inserted.

The basic validation can hold 4 values:

```
  - required        true|false
  - var-type        string|integer|boolean|..email
  - min             string: min-length, numbers: min-value
  - max             string: max-length, numbers: max-value
```

Examples

```
    const VALIDATION = [
        'name'     => [true, 'string', 2, 128],
        'email'    => [true, 'email', 0, 9],
        'age'      => [false, 'integer', 12, 99],
        'budget'   => [false, 'float'],
        ...
    ];

```

## Example Requests

### Validate a Login

Request

**POST** /users/validate_login

```json
    {
        "email": "john.doe@skynet.com",
        "password": "johnnydoe123"
    }
```

Response

```json
    {
        "id": 1,
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@skynet.com",
        "created_at": "2018-03-26 23:43:13",
        "updated_at": "2018-03-26 23:43:13",
        "attributes": {
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IntcInVzZXJJZFwiOjF9Ig.gIFAz2dVruyveohnmvnj4c4WLrOe7UNlEea0fMaHRpQ"
        }
    }
```


### Register a new User

Request

**POST** /users

```json
    {
	"firstname" : "John",
	"lastname": "Doe",
	"email":"john.doe@skynet.com",
	"password":"johnnydoe123"
    }
```

Response
```json
    {
        "id": 4,
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe11@gmail.com",
        "created_at": "2018-03-26 23:43:13",
        "updated_at": "2018-03-26 23:43:13",
        "attributes": {
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IntcInVzZXJfaWRcIjo0fSI.DbVv09BkwgVK8lSVuEWnuWe--H4q-Vitt9OwJa0_-Lk"
         }
    }
```

### Get all the Users

Request

**GET** /users

Response

```json
    [
        {
            "id": 1,
            "firstname": "John",
            "lastname": "Doe",
            "email": "john.doe@skynet.com",
            "created_at": "2018-03-26 23:43:13",
            "updated_at": "2018-03-26 23:43:13",
            "attributes": []
        },
        {
            "id": 2,
            "firstname": "Jane",
            "lastname": "Doe",
            "email": "jane.doe@skynet.com",
            "created_at": "2018-03-26 23:43:13",
            "updated_at": "2018-03-26 23:43:13",
            "attributes": []
        }
    ]
```

### Extra (end-note)

This may not be a high quality starting point, but this will get you started. Don't expect the most basic functions that other frameworks or seeds may have.
There is a lot of freedom to add your own functions, properties, .. but also a lot of possibilities for improvement (like the Model functions (getAll, insert, ..)).

Make sure to check that everything is secure, that there are no leaks or possibilities to insert non-allowed data etc.

The option to post issues for this repository is available, so I'll try to make sure that, if you have any issues or feedback, that I'll look into it.

You can always contact me at hello@bertmaurau.be for further information or feedback.

