<?php

namespace InoOicClient\Oic\Authorization;

use InoOicClient\Entity\AbstractEntity;
use InoOicClient\Util\ArgumentNormalizer;


/**
 * Authorization request.
 * 
 * @method void setEndpointUri(string $endpointUri)
 * @method void setClientId(string $clientId)
 * @method void setResponseType(string|array $responseType)
 * @method void setRedirectUri(string $redirectUri)
 * @method void setScope(mixed $scope)
 * @method void setState(string $state)
 * 
 * @method string getEndpointUri()
 * @method array getResponseType()
 * @method string getClientId()
 * @method string getRedirectUri()
 * @method array getScope()
 * @method string getState()
 */
class Request extends AbstractEntity
{


    /**
     * Constructor.
     * 
     * @param string $responseType
     * @param string $clientId
     * @param string $redirectUri
     * @param string $scope
     * @param string $state
     * @param array $extraParams
     */
    public function __construct($endpointUri, $responseType, $clientId, $redirectUri, $scope, $state = null, array $extraParams = array())
    {
        $this->setEndpointUri($endpointUri);
        $this->setResponseType($responseType);
        $this->setClientId($clientId);
        $this->setRedirectUri($redirectUri);
        $this->setScope($scope);
        $this->setState($state);
        
        foreach ($extraParams as $paramName => $paramValue) {
            $this->setProperty($paramName, $paramValue);
        }
    }


    protected function updateResponseType($responseType)
    {
        return ArgumentNormalizer::StringOrArrayToArray($responseType);
    }


    protected function updateScope($scope)
    {
        return ArgumentNormalizer::StringOrArrayToArray($scope);
    }
}