<?php

namespace InoOicClient\Oic\Authorization\Uri;

use Zend\Uri\Uri;
use InoOicClient\Oic\Authorization\Param;
use InoOicClient\Oic\Authorization\Request;


/**
 * Creates an URI based on the provided authorization request.
 */
class Generator
{

    protected $requiredParams = array(
        Param::CLIENT_ID,
        Param::REDIRECT_URI,
        Param::RESPONSE_TYPE,
        Param::SCOPE
    );


    /**
     * Generates an URI representing the authorization request.
     *
     * @param Request $request
     * @return string
     */
    public function createAuthorizationRequestUri(Request $request)
    {
        
        /* @var $clientInfo \InoOicClient\Client\ClientInfo */
        $clientInfo = $request->getClientInfo();
        if (! $clientInfo) {
            throw new \RuntimeException('Missing client info in request');
        }
        
        if (($endpointUri = $clientInfo->getAuthorizationEndpoint()) === null) {
            throw new Exception\MissingEndpointException('No endpoint specified');
        }
        
        $uri = new Uri($endpointUri);
        
        $params = array(
            Param::CLIENT_ID => $clientInfo->getClientId(),
            Param::REDIRECT_URI => $clientInfo->getRedirectUri(),
            Param::RESPONSE_TYPE => $this->arrayToSpaceDelimited($request->getResponseType()),
            Param::SCOPE => $this->arrayToSpaceDelimited($request->getScope()),
            Param::STATE => $request->getState()
        );
        
        foreach ($params as $name => $value) {
            if (in_array($name, $this->requiredParams) && empty($value)) {
                throw new Exception\MissingFieldException($name);
            }
        }
        
        $uri->setQuery($params);
        
        return $uri->toString();
    }


    protected function arrayToSpaceDelimited(array $list)
    {
        return implode(' ', $list);
    }
}