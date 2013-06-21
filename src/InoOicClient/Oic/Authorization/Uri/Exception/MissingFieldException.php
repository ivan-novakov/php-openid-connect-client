<?php

namespace InoOicClient\Oic\Authorization\Uri\Exception;


class MissingFieldException extends \RuntimeException
{

    protected $fieldName;


    public function __construct($fieldName)
    {
        $this->fieldName = $fieldName;
        parent::__construct(sprintf("Missing field '%s'", $fieldName));
    }


    public function getMissingFieldName()
    {
        return $this->fieldName;
    }
}