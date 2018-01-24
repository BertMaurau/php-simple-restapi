# Simple Restful API Example
A plain and simple PHP Restful API-base without the overhead of existing frameworks.

Just a simple PHP "seed" project that can be used to quickly get started with your own restful API. 

This includes

    - Routing: Allowed endpoints with their matching HTTP verb.
    - Middleware: Gets called before dispatching the route. can be used for Authentication, logging,..
    - MySQL Connection: Basic database connection.
    - Controllers: Handle the request, do magic, ..
    - Models: Handle the data.

## Installation

Clone this repo into your destination folder.

Download the required dependencies via Composer.
```
    composer install
```

Change the **config/Constants.php** values to your liking.

(optional)
To get started, you can import the provided .sql dump into your local database.
```
    database.sql
```

## Dependencies

   - PHP >= 5.4.0  
   - Route (https://github.com/thephpleague/route)  
   - Zend Diactoros (https://github.com/zendframework/zend-diactoros)  

## Modules

    - JWT (JSON Web Tokens) (https://jwt.io/)

## Structure

```
    - app
      - Constants.php           Contains salts, db credentials,..
      - Output.php              Contains the different Outputs (JSON and Exceptions)
      - DB.php			Handles the Database connection
      - Session.php		Handles the active User Session
    - controllers
      - BaseController.php      Handle the basic request actions (index, show, create, update, delete)
      - UserController.php      Handle User request actions (register, login)
    - middlewares
      - authentication.php      Contains the Authentication Middleware
    - models
      - BaseModel.php           Handle the basics (insert, update, map, delete,..)
      - User.php                Handle login, register
    - modules
      - JWT.php                 JWT Class
    - .htaccess                 Allow for pretty URLs, handle the Authentication Header
    - composer.json             Package information
    - index.php                 Brings everything together
```

## Endpoints

```
    GET     /me     			Get the currently logged-in User (showing the Session part) 
    GET     /users          		Get all Users ( allowed filters = ['firstname', 'lastname', 'email'] ex. /users?firstname=john )  
    GET     /users/{id}     		Get a specific User  
    POST    /users          		Create a new User  
    PATCH   /users/{id}     		Update an existing User   
    DELETE  /users/{id}     		Delete an existing User  
    POST    /users/validate_login	Validate the login based on post credentials
```

## Middelwares

The JWT (JSON Web Tokens) module is used for this basic example.  
You can use oAuth or whatever Authentication method you would like.  

After passing the Middleware, the 'user_data' will be available in the **Session** class and can be accessed like this:

```php
    Session::getUserId();
```

Response when the Token is invalid or missing
```json
    {
        "code": 401,
        "message": "Not Authorized!"
    }
``` 

## Session

I added a class called Session. This has nothing to do with an actual PHP session or anything.  
This class can be used to share stuff from the Middleware arround the API.

**For example.** After logging in, you decide to keep track of the UserID, the Language, other stuff.. and pass these values via the JWT Token. You can then add properties to the Session class (this has currently only the UserID). Then, when you need to access that value, you can call `Session::get{Property}();` to access that Token-shared-value.

The middleware loads the Token-stored-properties via `(new Session()) -> loadSession($decoded_JWT_array);`

## Register a new Route

When you want to add a new route to your API you can just add the following line to the index file

```php
    $route -> map(<HTTP_VERB>, <URI>, [new <CONTROLLER>, <ACTION>]) -> middleware(<MIDDLEWARE>);
```

Example
```php
    $route -> map('GET', '/users', [new UserController, 'index']) -> middleware($authentication);
```

Don't forget to define/create your controller to handle the requests actions. The 'index' refers to the function 'index' within the 'BaseController'.

## Create a new Controller

To create a new Controller (without extra functionalities) and use the basic endpoints.

```php
class NotesController extends BaseController
{
    // Set the current ModelName
    const MODEL_NAME = "Note";
}
```

## Create a new Model

To create a new Model (without extra functionalities) and use the basic functions.

```php
class Note extends BaseModel
{
    // Reference to the Database table
    const DB_TABLE = "notes";
    // Allowed filter params for the get requests
    const FILTERS = ['note'];
    // Does the table have timestamps?
    const TIMESTAMPS = false;
    
    // integer
    public $id;
    // string
    public $note;
    
    // GETTERS (optional)
    // SETTERS (optional) If no setters are defined, all values will be put under the `attributes` property.
}
```

## Example

### Register a new User

Request

**POST** /users

```json
    {
	"firstname" : "John",
	"lastname": "Doe",
	"email":"john.doe@gmail.com",
	"password":"f3823903b2dd6e35243b1bbe5a14f651"
    }
```

Response
```json
    {
         "id": 4,
         "firstname": "John",
         "lastname": "Doe",
         "email": "john.doe11@gmail.com",
         "attributes": {
             "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IntcInVzZXJfaWRcIjo0fSI.DbVv09BkwgVK8lSVuEWnuWe--H4q-Vitt9OwJa0_-Lk"
         }
    }
```

### Extra

This is not a high quality starting point, but this will get you started.  
There are a lot of possibilites for improvement like the Model functions (getAll, insert, ..).  

Make sure to check that everything is secure, that there are no leaks or possibilites to insert non-allowed data etc.

The index.php could use some cleanup with the requrirements, the routing, .. 
