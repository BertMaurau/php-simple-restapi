<?php

/**
 * Description of output
 *
 * @author Bert Maurau
 */
class Output
{

    // HTTP Codes
    const CODE_NOT_FOUND = 404;
    const CODE_CONFLICT = 406;
    const CODE_MISSING_PARAMETER = 406;
    const CODE_VALIDATION_FAILED = 406;
    const CODE_OK = 200;

    /**
     * JSON Output
     * @param Response $response
     * @param integer $code
     * @param any $data
     * @return Response
     */
    public static function JSON($response, $code, $data)
    {
        // maybe add some stuffs here
        // like extra validation, parameters, attributes, ..
        // actual output
        $response -> getBody()
                // write the output
                -> write(json_encode($data, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        // set the HTTP_CODE
        return $response -> withStatus($code);
    }

    /**
     * Return OK status
     * @param Response $response
     * @param any $data
     * @return JSON Output
     */
    public static function OK($response, $data)
    {
        return self::JSON($response, self::CODE_OK, $data);
    }

    /**
     * Return Model Not Found status
     * @param Response $response
     * @param string $modelName
     * @param any $modelId
     * @return JSON Output
     */
    public static function ModelNotFound($response, $modelName, $modelId)
    {
        return self::JSON($response, self::CODE_NOT_FOUND, array('code' => self::CODE_NOT_FOUND, 'message' => $modelName . ' with ID ' . $modelId . ' not found!'));
    }

    /**
     * Return Not Found status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function NotFound($response, $message)
    {
        return self::JSON($response, self::CODE_NOT_FOUND, array('code' => self::CODE_NOT_FOUND, 'message' => $message));
    }

    /**
     * Return Conflict status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function Conflict($response, $message)
    {
        return self::JSON($response, self::CODE_CONFLICT, array('code' => self::CODE_CONFLICT, 'message' => $message));
    }

    /**
     * Return Conflict status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function ValidationFailed($response, $message)
    {
        return self::JSON($response, self::CODE_VALIDATION_FAILED, array('code' => self::CODE_VALIDATION_FAILED, 'message' => $message));
    }

    /**
     * Return Missing Model Id status
     * @param Response $response
     * @return JSON Output
     */
    public static function MissingModelId($response)
    {
        return self::JSON($response, self::CODE_MISSING_PARAMETER, array('code' => self::CODE_MISSING_PARAMETER, 'message' => "Missing ModelID {id} parameter!"));
    }

}
