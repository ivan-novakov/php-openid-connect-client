<?php

namespace InoOicClient\Client;

use InoOicClient\Entity\AbstractEntity;
use InoOicClient\Oic\Authorization\Param;

/**
 * Client information container.
 * 
 * @method void setClientId(string $clientId)
 * @method void setRedirectUri(string $redirectUri)
 * @method void setAuthenticationInfo(\InoOicClient\Client\AuthenticationInfo $authentication)
 * 
 * @method string getClientId()
 * @method string getRedirectUri()
 * @method \InoOicClient\Client\AuthenticationInfo getAuthenticationInfo()
 */
class ClientInfo extends AbstractEntity
{

    protected $allowedProperties = array(
        Param::CLIENT_ID,
        Param::REDIRECT_URI,
        'authentication_info'
    );


    /**
     * Constructor.
     * 
     * @param string $clientId
     * @param string $redirectUri
     * @param AuthenticationInfo $authenticationInfo
     */
    public function __construct($clientId, $redirectUri, AuthenticationInfo $authenticationInfo)
    {
        $this->setClientId($clientId);
        $this->setRedirectUri($redirectUri);
        $this->setAuthenticationInfo($authenticationInfo);
    }
}