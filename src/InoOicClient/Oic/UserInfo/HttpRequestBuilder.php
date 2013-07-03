<?php

namespace InoOicClient\Oic\UserInfo;

use Zend\Http;
use InoOicClient\Oic\Token\Exception\InvalidRequestException;
use InoOicClient\Client\ClientInfo;


class HttpRequestBuilder
{

    protected $authHeaderName = 'Authorization';

    protected $tokenType = 'Bearer';


    /**
     * Creates a HTTP request based on the userinfo request.
     * 
     * @param Request $request
     * @param Http\Request $httpRequest
     * @return Http\Request
     */
    public function buildHttpRequest(Request $request, Http\Request $httpRequest = null)
    {
        if (null === $httpRequest) {
            $httpRequest = new Http\Request();
        }
        
        $clientInfo = $request->getClientInfo();
        if (! $clientInfo instanceof ClientInfo) {
            throw new InvalidRequestException('No client info in request');
        }
        
        $endpointUri = $clientInfo->getUserInfoEndpoint();
        
        $httpRequest->setUri($endpointUri);
        $httpRequest->setMethod(Http\Request::METHOD_GET);
        $httpRequest->getHeaders()->addHeaders(
            array(
                $this->authHeaderName => sprintf("%s %s", $this->tokenType, $request->getAccessToken())
            ));
        
        return $httpRequest;
    }
}