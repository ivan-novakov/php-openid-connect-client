<?php

namespace InoOicClient\Oic\UserInfo;

use InoOicClient\Entity\AbstractEntity;


/**
 * User info request.
 * 
 * @method void setAccessToken(string $accessToken)
 * @method void setClientInfo(\InoOicClient\Client\ClientInfo $clientInfo)
 * 
 * @method string getAccessToken()
 * @method \InoOicClient\Client\ClientInfo getClientInfo()
 */
class Request extends AbstractEntity
{

    const PROP_CLIENT_INFO = 'client_info';

    const PROP_ACCESS_TOKEN = 'access_token';

    protected $allowedProperties = array(
        self::PROP_CLIENT_INFO,
        self::PROP_ACCESS_TOKEN
    );
}