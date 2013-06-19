<?php

namespace InoOicClient\Oic\Authorization;

use InoOicClient\Server\ServerInfo;
use InoOicClient\Client\ClientInfo;
use InoOicClient\Entity\AbstractEntity;
use InoOicClient\Util\ArgumentNormalizer;


/**
 * Authorization request.
 * 
 * @method void setClientInfo(\InoOicClient\Client\ClientInfo $clientInfo)
 * @method void setServerInfo(\InoOicClient\Server\ServerInfo $serverInfo)
 * @method void setResponseType(string|array $responseType)
 * @method void setScope(mixed $scope)
 * @method void setState(string $state)
 * 
 * @method InoOicClient\Client\ClientInfo getClientInfo()
 * @method InoOicClient\Server\ServerInfo getServerInfo()
 * @method array getResponseType()
 * @method array getScope()
 * @method string getState()
 */
class Request extends AbstractEntity
{

    protected $allowedProperties = array(
        'client_info',
        'server_info',
        Param::RESPONSE_TYPE,
        Param::SCOPE,
        Param::STATE,
        Param::NONCE
    );


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
    public function __construct(ClientInfo $clientInfo, ServerInfo $serverInfo, $responseType, $scope, $state = null, 
        array $extraParams = array())
    {
        $this->setClientInfo($clientInfo);
        $this->setServerInfo($serverInfo);
        $this->setResponseType($responseType);
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