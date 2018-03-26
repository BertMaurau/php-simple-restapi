<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// ------------------------------------------------------------------------------
// Start of the routing definitions
// ------------------------------------------------------------------------------
// Index
$route -> map('GET', '/', function(ServerRequestInterface $request,
        ResponseInterface $response) {
    // You can do whatever you want here, This route is not required or anything.
    return Output::OK($response, "Hello World!");
});

// ------------------------------------------------------------------------------
// Recommended controller actions (to match the BaseController).
// ------------------------------------------------------------------------------
//  - List all (with filters, pagination, .. => index   (GET)
//  - Get a specific resource                => show    (GET)
//  - Update a resource                      => update  (PATCH)
//  - Delete a resource                      => delete  (DELETE)
//  - ..
// Choose whatever you feel like
// Model: User
// Basic Actions
// ------------------------------------------------------------------------------
// Get the currently logged-in user
$route -> map('GET', '/me', [new UserController, 'me'])
        -> middleware($authentication);

// List all Users (Paginate with take/skip parameters)
$route -> map('GET', '/users', [new UserController, 'index'])
        -> middleware($authentication);

// Get a specific User by ID
$route -> map('GET', '/users/{id}', [new UserController, 'show'])
        -> middleware($authentication);

// ------------------------------------------------------------------------------
// | Remark: Next two routes shouldn't be available for default Users.
// | This could allow them to update or delete another user.
// | Depends on your situation ofcourse, but you should make sure they have
// | the right privileges. You can validate them within the middleware function
// ------------------------------------------------------------------------------
// Update a specific User by ID
$route -> map('PATCH', '/users/{id}', [new UserController, 'update'])
        -> middleware($authentication);

// Get a specific User by ID
$route -> map('DELETE', '/users/{id}', [new UserController, 'delete'])
        -> middleware($authentication);

// ------------------------------------------------------------------------------
// Routes without authentication
// ------------------------------------------------------------------------------
// Validate the given Login
$route -> map('POST', '/users/validate_login', [new UserController, 'login']);

// Register a new User
$route -> map('POST', '/users', [new UserController, 'register']);

// ------------------------------------------------------------------------------
// Other Examples
// ------------------------------------------------------------------------------
// Grouped middleware and routes
//
// $route -> group('/customers', function ($route) {
//
//     $route -> map('GET',  '/',     [new CustomerController, 'index']);
//     $route -> map('GET',  '/{id}', [new CustomerController, 'show']);
//     $route -> map('POST', '/',     [new CustomerController, 'create']);
//     //...
//
// }) -> middleware($authentication);
//
// ------------------------------------------------------------------------------
// End of routing definitions. Dispatch the request to the controller and emit
// afterwards.
// ------------------------------------------------------------------------------