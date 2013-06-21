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
        /* @var $serverInfo \InoOicClient\Server\ServerInfo */
        $serverInfo = $request->getServerInfo();
        
        $uri = new Uri($serverInfo->getAuthorizationEndpoint());
        
        $params = array(
            Param::CLIENT_ID => $clientInfo->getClientId(),
            Param::REDIRECT_URI => $clientInfo->getRedirectUri(),
            Param::RESPONSE_TYPE => $this->arrayToSpaceDelimited($request->getResponseType()),
            Param::SCOPE => $this->arrayToSpaceDelimited($request->getScope()),
            Param::STATE => $request->getState()
        );
        $uri->setQuery($params);
        
        return $uri->toString();
    }


    protected function arrayToSpaceDelimited(array $list)
    {
        return implode(' ', $list);
    }
}