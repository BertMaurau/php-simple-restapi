<?php

/**
 * Description of UserController
 *
 * @author Bert Maurau
 */
class UserController extends BaseController
{

    // Set the current ModelName
    const MODEL_NAME = "User";

    // Handle me request
    public function me($request, $response)
    {
        // Call the BaseController `index` function
        // That function will return an Output, so no need to Output again.
        return $this -> show($request, $response, array('id' => Session::getUserId()));
    }

    // Handle login request
    public function login($request, $response)
    {
        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        // Do validation on posted fields..
        // ...
        // Check with database
        try {
            $user = (new User()) -> validateLogin($postdata -> email, $postdata -> password);
        } catch (\Exception $conflict) {
            // Gets thrown when there are multiple users found
            // should catch this as an 'MultipleLoginsFoundException' or something like that
            return Output::Conflict($response, $conflict -> getMessage());
        }
        if (!$user) {
            return Output::NotFound($response, 'No User found with given credentials!');
        }

        // Generate Token
        $token = JWT::encode(json_encode(array('userId' => $user -> getId())), Constants::JWT_SECRET);

        // Add the token to the response
        $user -> addAttribute('token', $token);

        return Output::OK($response, $user);
    }

    // Handle registration request
    public function register($request, $response)
    {
        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        // Do validation on posted fields..
        // ...
        // Check if email exists
        $user = (new User()) -> findBy('email', $postdata -> email, $take = 1);
        if ($user) {
            return Output::Conflict($response, 'User with email ' . $postdata -> email . ' already exists!');
        }

        // Build the User-model
        $user = (new User())
                -> setFirstname($postdata -> firstname)
                -> setLastname($postdata -> lastname)
                -> setEmail($postdata -> email)
                -> setPassword($postdata -> password)
                -> insert();

        // Do other stuffs like generating an avatar or such
        // you can access the insert user id via
        $id = $user -> getId();

        // Generate Token
        $token = JWT::encode(json_encode(array('userId' => $user -> getId())), Constants::JWT_SECRET);

        // Add the token to the response
        $user -> addAttribute('token', $token);

        return Output::OK($response, $user);
    }

}
