<?php

namespace InoOicClient\Entity;


interface PropertyMapperInterface
{


    public function camelCaseToProperty($camelCaseString);


    public function propertyToCamelCase($property);
}
