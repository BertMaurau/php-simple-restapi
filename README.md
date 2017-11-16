# Simple Restful API Example

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
    - - constants.php           Contains salts, db credentials,..
    - controllers
    - - UserController.php      Handle User request actions
    - database
    - - db.php                  Handle DB Connection
    - middlewares
    - - authentication.php      Contains the Authentication Middleware
    - models
    - - User.php                Handle the User data
    - modules
    - - JWT.php                 JWT Class
    - .htaccess                 Allow for pretty URLs, handle the Authentication Header
    - composer.json             Package information
    - index.php                 Brings everything together
```

## Endpoints

```
    GET     /users          Get all Users ( allowed filters = ['firstname', 'lastname', 'email'] ex. /users?firstname=john )  
    GET     /users/{id}     Get a specific User  
    POST    /users          Create a new User  
    PATCH   /users/{id}     Update an existing User   
    DELETE  /users/{id}     Delete an existing User  
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


Don't forget to define/create your controller to handle the requests actions. The 'index' refers to the function 'index' within the 'UserController'.

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
        "id": 1,
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@gmail.com",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IntcInVzZXJfaWRcIjoxfSI.irxM5UgwU5885UHSyXWEv45KYcdw1RRh6NKqZwW6goE"
    }
```

### 
