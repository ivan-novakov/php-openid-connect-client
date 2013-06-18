<?php

namespace InoOicClient\Util;


class ArgumentNormalizer
{


    static public function StringOrArrayToArray($argument)
    {
        $normalArgument = array();
        
        if (! is_array($argument)) {
            if (! is_string($argument)) {
                throw new \InvalidArgumentException("Invalid 'responseType' argument, expected string or array");
            }
            
            $argument = preg_split('/\s+/', $argument);
        }
        
        foreach ($argument as $key => $value) {
            $value = trim($value);
            if (is_string($value) && $value !== '') {
                $normalArgument[] = $value;
            }
        }
        
        return $normalArgument;
    }
}