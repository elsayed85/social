<?php

namespace App\Exceptions\Api\Postman;

use Exception;

class InvalidClientException extends Exception
{
    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getErrorType()
    {
        return $this->response->error;
    }

    public function getErrorMessage()
    {
        return $this->response->message;
    }

    public function getErrorDescription()
    {
        return $this->response->error_description;
    }
}
