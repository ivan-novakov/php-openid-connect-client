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
 * @method void setName(string $name)
 * @method void setDescription(string $description)
 * @method void setAuthorizationEndpoint(string $authorizationEndpoint)
 * @method void setTokenEndpoint(string $tokenEndpoint)
 * @method void setUserInfoEndpoint(stirng $userInfoEndpoint)
 * 
 * @method string getClientId()
 * @method string getRedirectUri()
 * @method AuthenticationInfo getAuthenticationInfo()
 * @method string getName()
 * @method string getDescription()
 * @method string getAuthorizationEndpoint()
 * @method string getTokenEndpoint()
 * @method string getUserInfoEndpoint()
 */
class ClientInfo extends AbstractEntity
{

    const AUTHENTICATION_INFO = 'authentication_info';

    const NAME = 'name';

    const DESCRIPTION = 'description';

    const AUTHORIZATION_ENDPOINT = 'authorization_endpoint';

    const TOKEN_ENDPOINT = 'token_endpoint';

    const USER_INFO_ENDPOINT = 'user_info_endpoint';

    protected $allowedProperties = array(
        Param::CLIENT_ID,
        Param::REDIRECT_URI,
        self::AUTHENTICATION_INFO,
        self::NAME,
        self::DESCRIPTION,
        self::AUTHORIZATION_ENDPOINT,
        self::TOKEN_ENDPOINT,
        self::USER_INFO_ENDPOINT
    );


    public function fromArray(array $properties, $replace = false)
    {
        if (isset($properties[self::AUTHENTICATION_INFO]) && is_array($properties[self::AUTHENTICATION_INFO])) {
            $authenticationInfo = new AuthenticationInfo();
            $authenticationInfo->fromArray($properties[self::AUTHENTICATION_INFO]);
            $this->setAuthenticationInfo($authenticationInfo);
            unset($properties[self::AUTHENTICATION_INFO]);
        }
        
        parent::fromArray($properties);
    }
}