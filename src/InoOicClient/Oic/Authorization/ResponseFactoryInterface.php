<?php

namespace InoOicClient\Oic\Authorization;


interface ResponseFactoryInterface
{


    /**
     * Creates an authorization response.
     * 
     * @param string $code
     * @param string $state
     */
    public function createResponse($code, $state = null);
}

