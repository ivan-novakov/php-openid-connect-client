<?php

namespace InoOicClient\Oic\UserInfo;


class ResponseFactory implements ResponseFactoryInterface
{


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\UserInfo\ResponseFactoryInterface::createResponse()
     */
    public function createResponse(array $responseData)
    {
        $response = new Response();
        $response->setClaims($responseData);
        
        return $response;
    }
}