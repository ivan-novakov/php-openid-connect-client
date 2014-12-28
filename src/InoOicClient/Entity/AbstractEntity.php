<?php

namespace InoOicClient\Entity;

use Zend\Stdlib\Parameters;
use Zend\Stdlib\ArrayObject;


abstract class AbstractEntity
{

    /**
     * Entity properties.
     * @var Parameters
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
     * Returns all entity properties in a parameters container.
     * 
     * @return Parameters
     */
    public function getProperties($initialize = false)
    {
        if ($initialize || null === $this->properties) {
            $this->properties = $this->initProperties();
        }
        
        return $this->properties;
    }


    /**
     * Sets the entity properties from an array.
     *
     * @param array $properties
     * @param boolean $replace
     */
    public function fromArray(array $properties, $replace = false)
    {
        if ($replace) {
            $this->properties = $this->initProperties();
        }
        
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
     * Returns the entity properties as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $values = array();
        foreach ($this->getProperties() as $name => $value) {
            $getterName = $this->createGetterName($name);
            $values[$name] = call_user_func_array(array(
                $this,
                $getterName
            ), array());
        }
        
        return $values;
    }


    public function __call($methodName, array $arguments)
    {
        if (preg_match('/^get(\w+)$/', $methodName, $matches)) {
            $propertyName = $this->getPropertyMapper()->camelCaseToProperty($matches[1]);
            return $this->getProperty($propertyName);
        }
        
        if (preg_match('/set(\w+)$/', $methodName, $matches)) {
            $methodFragment = $matches[1];
            $value = $arguments[0];
            
            $updateMethod = 'update' . $methodFragment;
            if (method_exists($this, $updateMethod)) {
                $value = call_user_func_array(array(
                    $this,
                    $updateMethod
                ), array(
                    $value
                ));
            }
            
            $propertyName = $this->getPropertyMapper()->camelCaseToProperty($methodFragment);
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
        if ($this->isAllowedProperty($name)) {
            $this->getProperties()->set($name, $value);
        }
    }


    /**
     * Returns the value of a property.
     *
     * @param string $name
     * @return mixed|null
     */
    protected function getProperty($name, $default = null)
    {
        if (! $this->isAllowedProperty($name)) {
            return null;
        }
        
        return $this->getProperties()->get($name, $default);
    }


    /**
     * Generates a setter name for the property.
     *
     * @param string $propertyName
     * @return string
     */
    protected function createSetterName($propertyName)
    {
        return sprintf("set%s", $this->getPropertyMapper()->propertyToCamelCase($propertyName));
    }


    /**
     * Generates a getter name for the property.
     *
     * @param string $propertyName
     * @return string
     */
    protected function createGetterName($propertyName)
    {
        return sprintf("get%s", $this->getPropertyMapper()->propertyToCamelCase($propertyName));
    }


    /**
     * Checks if the property is allowed and throws an exception otherwise.
     * 
     * @param string $propertyName
     * @throws Exception\UnknownPropertyException
     */
    protected function checkAllowedProperty($propertyName)
    {
        if (! $this->isAllowedProperty($propertyName)) {
            throw new Exception\UnknownPropertyException($propertyName);
        }
    }


    /**
     * Returns true, if the property is allowed.
     * 
     * @param string $propertyName
     * @return boolean
     */
    protected function isAllowedProperty($propertyName)
    {
        if (is_array($this->allowedProperties) && ! in_array($propertyName, $this->allowedProperties)) {
            return false;
        }
        
        return true;
    }


    /**
     * Initializes an empty properties container.
     * 
     * @return Parameters
     */
    protected function initProperties()
    {
        return new Parameters();
    }
}