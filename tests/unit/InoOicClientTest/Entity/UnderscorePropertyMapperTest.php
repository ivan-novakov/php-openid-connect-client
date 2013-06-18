<?php

namespace InoOicClientTest\Entity;

use InoOicClient\Entity\UnderscorePropertyMapper;


class UnderscorePropertyMapperTest extends \PHPUnit_Framework_TestCase
{

    protected $mapper;


    public function setUp()
    {
        $this->mapper = new UnderscorePropertyMapper();
    }


    public function testCamelCaseToProperty()
    {
        $this->assertSame('camel_case_string', $this->mapper->camelCaseToProperty('CamelCaseString'));
    }


    public function testPropertyToCamelCase()
    {
        $this->assertSame('SomeRandomProperty', $this->mapper->propertyToCamelCase('some_random_property'));
    }
}