<?php

namespace InoOicClient\Entity;

use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\UnderscoreToCamelCase;


class UnderscorePropertyMapper implements PropertyMapperInterface
{


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Entity\PropertyMapperInterface::camelCaseToProperty()
     */
    public function camelCaseToProperty($camelCaseString)
    {
        return $this->camelCaseToUnderscore($camelCaseString);
    }


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Entity\PropertyMapperInterface::propertyToCamelCase()
     */
    public function propertyToCamelCase($property)
    {
        return $this->underscoreToCamelCase($property);
    }


    /**
     * Converts an underscore delimited string to a camel case string.
     *
     * @param string $value
     * @param boolean $capitalize
     * @return string
     */
    protected function underscoreToCamelCase($value, $capitalize = true)
    {
        $filter = new UnderscoreToCamelCase();
        $value = $filter->filter($value);
        if (! $capitalize) {
            $value = lcfirst($value);
        }
        
        return $value;
    }


    /**
     * Converts a camel case string into an underscore delimited string.
     *
     * @param string $value
     * @param boolean $lowerCase
     * @return string
     */
    protected function camelCaseToUnderscore($value, $lowerCase = true)
    {
        $filter = new CamelCaseToUnderscore();
        $value = $filter->filter($value);
        if ($lowerCase) {
            $value = strtolower($value);
        }
        
        return $value;
    }
}