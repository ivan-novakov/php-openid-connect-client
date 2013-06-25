<?php

namespace InoOicClient\Oic\Exception;

use InoOicClient\Oic\Error;


class ErrorResponseException extends \RuntimeException
{

    /**
     * The error object.
     * @var Error
     */
    protected $error;


    /**
     * Constructor.
     * 
     * @param Error $error
     */
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


    /**
     * Returns the error object.
     *
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }
}