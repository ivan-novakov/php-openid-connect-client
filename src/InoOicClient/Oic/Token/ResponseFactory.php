<?php

namespace InoOicClient\Oic\Token;


class ResponseFactory implements ResponseFactoryInterface
{


    /**
     * {@inhertidoc}
     * @see \InoOicClient\Oic\Token\ResponseFactoryInterface::createResponse()
     */
    public function createResponse(array $responseData)
    {
        $response = new Response();
        $response->fromArray($responseData);
        
        return $response;
    }
}