<?php

namespace InoOicClient\Entity;

use Zend\Stdlib\ArrayObject;


abstract class AbstractEntity
{

    /**
     * Entity properties.
     * @var ArrayObject
     */
    protected $properties;

    /**
     * Property mapper - maps properties to magic method fragments.
     * @var PropertyMapperInterface
     */
    protected $propertyMapper;

    /**
     * List of properties allowed to be set for the entity.
     * @var array|null
     */
    protected $allowedProperties;


    /**
     * Returns the property mapper.
     * 
     * @return PropertyMapperInterface
     */
    public function getPropertyMapper()
    {
        if (null === $this->propertyMapper) {
            $this->propertyMapper = new UnderscorePropertyMapper();
        }
        
        return $this->propertyMapper;
    }


    /**
     * Sets the property mapper.
     * 
     * @param PropertyMapperInterface $propertyMapper
     */
    public function setPropertyMapper(PropertyMapperInterface $propertyMapper)
    {
        $this->propertyMapper = $propertyMapper;
    }


    /**
     * Returns the entity properties as ArrayObject.
     * 
     * @return ArrayObject
     */
    public function getProperties($initialize = false)
    {
        if ($initialize || null === $this->properties) {
            $this->properties = new ArrayObject();
        }
        
        return $this->properties;
    }


    /**
     * Sets the entity properties.
     *
     * @param array $properties
     */
    public function fromArray(array $properties)
    {
        foreach ($properties as $name => $value) {
            $setterName = $this->createSetterName($name);
            call_user_func_array(array(
                $this,
                $setterName
            ), array(
                $value
            ));
        }
    }


    /**
     * Returns the entity properties.
     *
     * @return array
     */
    public function toArray()
    {
        $values = array();
        foreach ($this->getProperties() as $name => $value) {
            $getterName = $this->createGetterName($name);
            $values[$name] = call_user_func_array(
                array(
                    $this,
                    $getterName
                ), array());
        }
        
        return $values;
    }


    public function __call($methodName, array $arguments)
    {
        if (preg_match('/^get(\w+)$/', $methodName, $matches)) {
            $propertyName = $this->getPropertyMapper()
                ->camelCaseToProperty($matches[1]);
            return $this->getProperty($propertyName);
        }
        
        if (preg_match('/set(\w+)$/', $methodName, $matches)) {
            $methodFragment = $matches[1];
            $value = $arguments[0];
            
            $updateMethod = 'update' . $methodFragment;
            if (method_exists($this, $updateMethod)) {
                $value = call_user_func_array(
                    array(
                        $this,
                        $updateMethod
                    ), array(
                        $value
                    ));
            }
            
            $propertyName = $this->getPropertyMapper()
                ->camelCaseToProperty($methodFragment);
            return call_user_func_array(array(
                $this,
                'setProperty'
            ), array(
                $propertyName,
                $value
            ));
        }
        
        throw new Exception\InvalidMethodException(sprintf("Invalid method %s::%s()", get_class($this), $methodName));
    }


    /**
     * Sets the value of a single property.
     *
     * @param string $name
     * @param mixed $value
     */
    protected function setProperty($name, $value)
    {
        if (! $this->isAllowedProperty($name)) {
            throw new Exception\UnknownPropertyException($name);
        }
        
        $this->getProperties()
            ->offsetSet($name, $value);
    }


    /**
     * Returns the value of a property.
     *
     * @param string $name
     * @return mixed|null
     */
    protected function getProperty($name)
    {
        if (! $this->isAllowedProperty($name)) {
            throw new Exception\UnknownPropertyException($name);
        }
        
        $properties = $this->getProperties();
        if ($properties->offsetExists($name)) {
            return $properties->offsetGet($name);
        }
        
        return null;
    }


    /**
     * Generates a setter name for the property.
     *
     * @param string $propertyName
     * @return string
     */
    protected function createSetterName($propertyName)
    {
        return sprintf("set%s", $this->getPropertyMapper()
            ->propertyToCamelCase($propertyName));
    }


    /**
     * Generates a getter name for the property.
     *
     * @param string $propertyName
     * @return string
     */
    protected function createGetterName($propertyName)
    {
        return sprintf("get%s", $this->getPropertyMapper()
            ->propertyToCamelCase($propertyName));
    }


    protected function isAllowedProperty($propertyName)
    {
        if (is_array($this->allowedProperties) && ! in_array($propertyName, $this->allowedProperties)) {
            return false;
        }
        
        return true;
    }
}