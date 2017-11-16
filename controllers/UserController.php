<?php

/**
 * Description of UserController
 *
 * @author Bert Maurau
 */
class UserController
{

    // Handle the main index GET
    public function index($request, $response)
    {
        // Maybe handle the GET query string better.
        $users = (new User()) -> getAll($_GET);

        $response -> getBody() -> write(json_encode($users));
        return $response -> withStatus(200);
    }

    // Handle model request
    public function show($request, $response, $args)
    {
        $modelId = $args['id'];

        $user = (new User()) -> getById($modelId);
        if (!$user) {
            $response -> getBody() -> write(json_encode(array('code' => 404, 'message' => 'User w/ ID ' . $modelId . ' not found!')));
            return $response -> withStatus(404);
        }

        $response -> getBody() -> write(json_encode($user));
        return $response -> withStatus(200);
    }

    // Handle login request
    public function login($request, $response)
    {
        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        // Do validation on posted fields..
        // ...
        // Check with database
        $user = (new User()) -> validateLogin($request -> email, $request -> password);
        if (!$user) {
            $response -> getBody() -> write(json_encode(array('code' => 404, 'message' => 'No User found w/ given credentials!')));
            return $response -> withStatus(404);
        }

        // Generate Token
        $token = JWT::encode(json_encode(array('user_id' => $user -> getId())), Constants::JWT_SECRET);

        // Add the token to the response
        $user -> token = $token;

        $response -> getBody() -> write(json_encode($user));
        return $response -> withStatus(200);
    }

    // Handle registration request
    public function register($request, $response)
    {
        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        // Do validation on posted fields..
        // ...
        // Check if email exists
        $user = (new User()) -> findBy('email', $postdata -> email);
        if ($user) {
            $response -> getBody() -> write(json_encode(array('code' => 406, 'message' => 'User w/ email ' . $postdata -> email . ' already exists!')));
            return $response -> withStatus(406);
        }

        // Build the User-model
        $user = (new User())
                -> setFirstname($postdata -> firstname)
                -> setLastname($postdata -> lastname)
                -> setEmail($postdata -> email)
                -> setPassword($postdata -> password)
                -> insert();

        // Generate Token
        $token = JWT::encode(json_encode(array('user_id' => $user -> getId())), Constants::JWT_SECRET);

        // Add the token to the response
        $user -> token = $token;

        $response -> getBody() -> write(json_encode($user));
        return $response -> withStatus(200);
    }

    // Handle an update request
    public function update($request, $response, $args)
    {
        $modelId = $args['id'];

        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        $user = (new User()) -> getById($modelId);
        if (!$user) {
            $response -> getBody() -> write(json_encode(array('code' => 404, 'message' => 'User w/ ID ' . $modelId . ' not found!')));
            return $response -> withStatus(404);
        }

        $user -> createObjectFromProperties($postdata) -> update();

        $response -> getBody() -> write(json_encode($user));
        return $response -> withStatus(200);
    }

    // Handle a delete request
    public function delete($request, $response, $args)
    {
        $modelId = $args['id'];

        $user = (new User()) -> getById($modelId);
        if (!$user) {
            $response -> getBody() -> write(json_encode(array('code' => 404, 'message' => 'User w/ ID ' . $modelId . ' not found!')));
            return $response -> withStatus(404);
        }

        $user -> delete();

        $response -> getBody() -> write(json_encode($user));
        return $response -> withStatus(200);
    }

}
