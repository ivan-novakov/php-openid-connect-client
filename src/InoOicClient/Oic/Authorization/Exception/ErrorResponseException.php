<?php

namespace InoOicClient\Oic\Authorization\Exception;

use InoOicClient\Oic\Error;


class ErrorResponseException extends \RuntimeException
{

    protected $error;


    public function __construct(Error $error)
    {
        $this->error = $error;
        
        $message = sprintf("Error response from server '%s'", $error->getCode());
        
        if ($description = $error->getDescription()) {
            $message .= " ($description)";
        }
        
        if ($uri = $error->getUri()) {
            $message .= ", more info: $uri";
        }
        
        parent::__construct($message);
    }


    public function getError()
    {
        return $this->error;
    }
}