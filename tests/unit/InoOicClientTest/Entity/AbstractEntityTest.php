<?php

namespace InoOicClientTest\Entity;


class AbstractEntityTest extends \PHPUnit_Framework_TestCase
{

    protected $entity = null;


    public function setUp()
    {
        include_once TESTS_ROOT . '/_files/AbstractEntitySubclass.php';
        $this->entity = $this->getMockForAbstractClass('InoOicClient\Entity\AbstractEntity');
    }


    public function testMagicGetter()
    {
        $value = 'test value';
        $property = 'foo_bar';
        $camelCase = 'FooBar';
        
        $this->assertNull($this->entity->getFooBar());
        
        $this->entity->fromArray(array(
            $property => $value
        ));
        
        $mapper = $this->createMapperMock();
        $mapper->expects($this->once())
            ->method('camelCaseToProperty')
            ->with($camelCase)
            ->will($this->returnValue($property));
        $this->entity->setPropertyMapper($mapper);
        
        $this->assertSame($value, $this->entity->getFooBar());
    }


    public function testMagicSetter()
    {
        $value = 'test value';
        $property = 'foo_bar';
        $camelCase = 'FooBar';
        
        $this->assertNull($this->entity->getProperty($property));
        
        $mapper = $this->createMapperMock();
        $mapper->expects($this->any())
            ->method('camelCaseToProperty')
            ->with($camelCase)
            ->will($this->returnValue($property));
        $mapper->expects($this->any())
            ->method('propertyToCamelCase')
            ->with($property)
            ->will($this->returnValue($camelCase));
        
        $this->entity->setPropertyMapper($mapper);
        
        $this->entity->setFooBar($value);
        
        $properties = $this->entity->toArray();
        $this->assertArrayHasKey($property, $properties);
        $this->assertSame($value, $properties[$property]);
    }


    public function testInvalidMagicCall()
    {
        $this->setExpectedException('InoOicClient\Entity\Exception\InvalidMethodException');
        
        $this->entity->someInvalidCall();
    }


    public function testFromArray()
    {
        $properties = array(
            'foo' => 'bar'
        );
        $this->entity->fromArray($properties);
        $this->assertSame($properties, $this->entity->toArray());
    }


    public function testSetUnknownProperty()
    {
        $this->setExpectedException('InoOicClient\Entity\Exception\UnknownPropertyException');
        
        $entity = new \AbstractEntitySubclass();
        $entity->setBar('something');
    }


    public function testGetUnknownProperty()
    {
        $this->setExpectedException('InoOicClient\Entity\Exception\UnknownPropertyException');
        
        $entity = new \AbstractEntitySubclass();
        $entity->getBar();
    }
    
    
    public function testSetKnownProperty()
    {
        $value = 'testvalue';
        $entity = new \AbstractEntitySubclass();
        $entity->setFoo($value);
        $this->assertSame($value, $entity->getFoo());
    }


    protected function createMapperMock()
    {
        $mapper = $this->getMock('InoOicClient\Entity\PropertyMapperInterface');
        
        return $mapper;
    }
}
