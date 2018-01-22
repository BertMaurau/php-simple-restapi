<?php

/**
 * The actual middleware function that gets called upon routing
 */
$authentication = function ($request, $response, callable $next) {

    // Get the Token
    $token = getBearerToken();
    if ($token) {
        try {
            // Get the JSON data that has been encoded (can contain whatever you like)
            // and add that data to the request object that gets passed to the controllers
            $request -> user_data = (object) json_decode(JWT::decode($token, Constants::JWT_SECRET));
        } catch (Exception $ex) {
            // Send response when the integrity of the Token doesn't failed
            $response -> getBody() -> write(Output::JSON(array("code" => 401, "message" => "Not Authorized!")));
            return $response -> withStatus(401);
        }
    } else {
        // The response when there is no Token present
        $response -> getBody() -> write(Output::JSON(array("code" => 401, "message" => "Not Authorized!")));
        return $response -> withStatus(401);
    }

    // Continue the request if the user is allowed
    $response = $next($request, $response);
    return $response;
};

/**
 * Get the Authorization header from the request Headers
 * @return string $header
 */
function getAuthorizationHeader()
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();

        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/**
 * Check and get the Bearer token from the header
 * @return string token
 */
function getBearerToken()
{
    $headers = getAuthorizationHeader();

    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        // Allow for the usage of the token as a GET parameter
    } else if (isset($_GET['token'])) {
        return $_GET['token'];
    } else {
        return null;
    }
}
