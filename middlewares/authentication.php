<?php

/**
 * The actual middleware function that gets called upon routing.
 * You can do whatever you'd like here before continuing executing the requested
 * actions etc.
 */
$authentication = function ($request, $response, callable $next) {

    // Get the Token from the headers
    $token = getBearerToken();
    // check if there's a token present
    if ($token) {
        try {
            // Get the JSON data that has been encoded (can contain whatever you like)
            // and add that data to the request object that gets passed to the controllers
            $tokenProperties = (object) json_decode(JWT::decode($token, JWT_SECRET));
            // Load into the Auth class
            (new Auth()) -> loadAuth($tokenProperties);
        } catch (Exception $ex) {
            // Send response when the integrity-check of the Token failed.
            // This will happen if the passed encrypted data is not a valid JSON-string
            // due to a wrong encryption or bad encoding.
            $response -> getBody() -> write(Output::JSON(array("code" => 401, "message" => "Token integrity-check failed!")));
            return $response -> withStatus(401);
        }
    } else {
        // The response when there is no Token present
        $response -> getBody() -> write(Output::JSON(array("code" => 401, "message" => "No Token present!")));
        return $response -> withStatus(401);
    }

    // Continue the request if the user is allowed (passed the above checks)
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
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();

        // Server-side fix for bug in old Android versions (a nice side-effect of
        // this fix means we don't care about capitalization for Authorization)
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
    // Get the headers first.
    $headers = getAuthorizationHeader();

    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    } else {

        // Allow for the usage of the token as a GET parameter
        if (ALLOW_URL_GET_TOKEN && isset($_GET[GET_TOKEN_PARAM])) {
            return $_GET['token'];
        } else {
            return null;
        }
    }
}
