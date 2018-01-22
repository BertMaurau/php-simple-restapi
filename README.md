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

```
    composer install
```

Import the provided database dump to get started.
```
    database.sql
```

Change the config/constants.php values to your liking.

## Dependencies

    - Route (https://github.com/thephpleague/route)
    - Zend Diactoros (https://github.com/zendframework/zend-diactoros)

## Modules

    - JWT (JSON Web Tokens) (https://jwt.io/)

## Structure

```
    - config
    - - Constants.php           Contains salts, db credentials,..
    - - Output.php              Contains the different Outputs (JSON and Exceptions)
    - controllers
    - - BaseController.php      Handle the basic request actions (index, show, create, update, delete)
    - - UserController.php      Handle User request actions (register, login)
    - database
    - - db.php                  Handle DB Connection
    - middlewares
    - - authentication.php      Contains the Authentication Middleware
    - models
    - - BaseModel.php           Handle the basics (insert, update, map, delete,..)
    - - User.php                Handle login, register
    - modules
    - - JWT.php                 JWT Class
    - .htaccess                 Allow for pretty URLs, handle the Authentication Header
    - composer.json             Package information
    - index.php                 Brings everything together
```

## Endpoints

```
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

After passing the Middleware, the 'user_data' will be available in the **$request** variable and can be accessed like this:

```php
    $id_of_user_logged_in = $request -> user_data -> user_id;
```

Response when the Token is invalid or missing
```json
    {
        "code": 401,
        "message": "Not Authorized!"
    }
``` 

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

### 
