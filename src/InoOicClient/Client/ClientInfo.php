<?php

namespace InoOicClient\Client;

use InoOicClient\Entity\AbstractEntity;
use InoOicClient\Oic\Authorization\Param;


/**
 * Client information container.
 * 
 * @method void setClientId(string $clientId)
 * @method void setRedirectUri(string $redirectUri)
 * @method void setAuthenticationInfo(AuthenticationInfo $authentication)
 * 
 * @method string getClientId()
 * @method string getRedirectUri()
 * @method AuthenticationInfo getAuthenticationInfo()
 */
class ClientInfo extends AbstractEntity
{

    protected $allowedProperties = array(
        Param::CLIENT_ID,
        Param::REDIRECT_URI,
        'authentication_info'
    );
}