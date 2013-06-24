<?php

namespace InoOicClient\Oic\Token;

use InoOicClient\Entity\AbstractEntity;


/**
 * Token request.
 * 
 * @method void setClientInfo(\InoOicClient\Client\ClientInfo $clientInfo)
 * @method void setServerInfo(\InoOicClient\Server\ServerInfo $serverInfo)
 * @method void setGrantType(string $grantType)
 * @method void setCode(string $code)
 * 
 * @method \InoOicClient\Client\ClientInfo getClientInfo()
 * @method \InoOicClient\Server\ServerInfo getServerInfo()
 * @method string getGrantType()
 * @method string getCode()
 */
class Request extends AbstractEntity
{

    protected $allowedProperties = array(
        'client_info',
        'server_info',
        Param::GRANT_TYPE,
        Param::CODE
    );
}