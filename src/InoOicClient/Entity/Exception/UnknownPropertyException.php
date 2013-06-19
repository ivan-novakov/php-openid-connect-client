<?php

namespace InoOicClient\Entity\Exception;


class UnknownPropertyException extends \RuntimeException
{

    protected $propertyName = null;


    public function __construct($propertyName)
    {
        $this->propertyName = $propertyName;
        parent::__construct(sprintf("Unknown property '%s'", $this->propertyName));
    }


    public function getPropertyName()
    {
        return $this->propertyName;
    }
}