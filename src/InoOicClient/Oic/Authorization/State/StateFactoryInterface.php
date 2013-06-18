<?php

namespace InoOicClient\Oic\Authorization\State;


interface StateFactoryInterface
{


    /**
     * Creates and returns the state entity.
     * 
     * @param string $requestUri
     */
    public function createState($requestUri = null);
}

