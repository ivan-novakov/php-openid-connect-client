<?php

namespace InoOicClient\Oic\UserInfo;


interface ResponseFactoryInterface
{


    /**
     * Creates a user info response entity.
     * 
     * @param array $responseData
     * @return Response $response
     */
    public function createResponse(array $responseData);
}
