<?php

/**
 * Description of UserController
 *
 * This is just an example controller to show you how you can handle custom
 * actions, custom model functions and how the Controller definition works with
 * the active model class etc.
 *
 * @author Bert Maurau
 */
class UserController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = "User";

    // Handle `user/me` request
    public function me($request, $response)
    {
        // Call the BaseController `show` action to get only one value for the
        // given resource ID.
        //
        // That function will return an Output, so no need to Output it again.
        //
        // In case the ID wasn't found, it will return a 404 output etc.
        // Handle anything else if needed, but for the current exmaple, we'll just
        // return that output.
        return $this -> show($request, $response, array('id' => Auth::getUserId()));
    }

    // Handle the `user/login` request
    public function login($request, $response)
    {
        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        // Do validation on posted fields if needed..
        // ...
        // Check with database.
        try {
            // Call the User-model 'validateLogin' function with the given credentials.
            $user = (new User()) -> validateLogin($postdata -> email, $postdata -> password);
        } catch (\Exception $conflict) {
            // Gets thrown when there are multiple users found (Defined in the model function)
            // should catch this as an 'MultipleLoginsFoundException' or something like that.
            return Output::Conflict($response, $conflict -> getMessage());
        }
        // If the user wasn't found..
        if (!$user) {
            return Output::NotFound($response, 'No User found with given credentials!');
        }

        // If everything seems to be correct and valid, generate a new JWT Token.
        // json_encode the data for future decoding etc and maybe even front-end
        // handeling.
        $token = JWT::encode(json_encode(array(
                    'userId' => $user -> getId())), JWT_SECRET);

        // Add the generated token to the response
        $user -> addAttribute('token', $token);

        // Output the User model.
        return Output::OK($response, $user);
    }

    // Handle the `user/registration` request (post)
    public function register($request, $response)
    {
        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        // Do validation on posted fields..
        // ...
        // Check if the email exists.
        // The take=1 will return one item and not an array of 1 item.
        $user = (new User()) -> findBy('email', $postdata -> email, $take = 1);
        if ($user) {
            // We found a user with that email. Output that.
            return Output::Conflict($response, 'User with email ' . $postdata -> email . ' already exists!');
        }

        // Build a new User-model
        $user = (new User())
                -> setFirstname($postdata -> firstname)
                -> setLastname($postdata -> lastname)
                -> setEmail($postdata -> email)
                -> setPassword($postdata -> password)
                -> insert();

        // Do other stuffs like generating an avatar or such
        // you can access the inserted userId via
        $id = $user -> getId();
        // But this will be automatically added to the model.
        // Generate a new JWT Token
        $token = JWT::encode(json_encode(array(
                    'userId' => $user -> getId())), JWT_SECRET);

        // Add the token to the response
        $user -> addAttribute('token', $token);

        // Output the newly generated User-model
        return Output::OK($response, $user);
    }

}
