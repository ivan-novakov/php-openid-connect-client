<?php

namespace InoOicClient\Client\Authenticator\Exception;


class MissingParameterException extends \RuntimeException
{

    protected $missingParameter;


    public function __construct($parameter)
    {
        $this->missingParameter = $parameter;
        parent::__construct(sprintf("Missing parameter '%s'", $this->missingParameter));
    }


    public function getMissingParameter()
    {
        return $this->missingParameter;
    }
}