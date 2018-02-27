<?php

/**
 * Description of BaseController
 *
 * This handles the basic requests like
 *  - index  [GET]    (List all models)
 *  - show   [GET]    (List a specific model)
 *  - create [POST]   (Insert a new model)
 *  - update [PATCH]  (Update a specific model)
 *  - delete [DELETE] (Delete a specific model)
 *
 * @author Bert Maurau
 */
class BaseController
{

    public static $modelName = "";

    // Handle the main index GET
    public function index($request, $response)
    {
        $modelClass = static::MODEL_NAME;
        // Maybe handle the GET query string better.
        // Non-allowed filters will get filtered out
        $models = (new $modelClass()) -> getAll($_GET);

        return Output::OK($response, $models);
    }

    // Handle model request
    public function show($request, $response, $args)
    {
        $modelClass = static::MODEL_NAME;

        // check for modelId
        if (!isset($args['id'])) {
            return Output::MissingModelId($response);
        }

        $modelId = $args['id'];

        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            return Output::ModelNotFound($response, $modelName, $modelId);
        }

        return Output::OK($response, $model);
    }

    // Handle the create request
    public function create($request, $response)
    {
        $modelClass = static::MODEL_NAME;

        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        $model -> map($postdata);

        // apply validation rules to the mapped object before inserting
        if (!$validator[0] = $model -> validate()) {
            return Output::ValidationFailed($response, $validator[1]);
        }
        $model -> insert();


        return Output::OK($response, $model);
    }

    // Handle an update request
    public function update($request, $response, $args)
    {
        $modelClass = static::MODEL_NAME;

        // check for modelId
        if (!isset($args['id'])) {
            return Output::MissingModelId($response);
        }

        $modelId = $args['id'];

        // Get the POST body
        $postdata = (object) json_decode($request -> getBody(), true);

        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            return Output::ModelNotFound($response, $modelClass, $model);
        }

        $model -> map($postdata);

        // apply validation rules to the mapped object before inserting
        if (!$validator[0] = $model -> validate()) {
            return Output::ValidationFailed($response, $validator[1]);
        }
        $model -> update();

        return Output::OK($response, $model);
    }

    // Handle a delete request
    public function delete($request, $response, $args)
    {
        $modelClass = static::MODEL_NAME;

        // check for modelId
        if (!isset($args['id'])) {
            return Output::MissingModelId($response);
        }

        $modelId = $args['id'];

        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            return Output::ModelNotFound($response, $modelClass, $modelId);
        }

        $model -> delete();

        return Output::OK($response, $model);
    }

}
