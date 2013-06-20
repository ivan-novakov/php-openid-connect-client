<?php

namespace InoOicClient\Oic\Authorization;


class ResponseFactory implements ResponseFactoryInterface
{


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\Authorization\ResponseFactoryInterface::createResponse()
     */
    public function createResponse($code, $state = null)
    {
        $response = new Response();
        $response->setCode($code);
        if (null !== $state) {
            $response->setState($state);
        }
        
        return $response;
    }
}