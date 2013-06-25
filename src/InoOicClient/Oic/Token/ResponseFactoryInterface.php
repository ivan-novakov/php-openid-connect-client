<?php

namespace InoOicClient\Oic\Token;


interface ResponseFactoryInterface
{


    /**
     * Creates a token response.
     * 
     * @param array $responseData
     * @return Response
     */
    public function createResponse(array $responseData);
}