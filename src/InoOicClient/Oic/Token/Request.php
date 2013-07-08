<?php

namespace InoOicClient\Oic\Token;

use InoOicClient\Entity\AbstractEntity;


/**
 * Token request.
 * 
 * @method void setClientInfo(\InoOicClient\Client\ClientInfo $clientInfo)
 * @method void setGrantType(string $grantType)
 * @method void setCode(string $code)
 * 
 * @method \InoOicClient\Client\ClientInfo getClientInfo()
 * @method string getGrantType()
 * @method string getCode()
 */
class Request extends AbstractEntity
{

    const CLIENT_INFO = 'client_info';

    protected $allowedProperties = array(
        self::CLIENT_INFO,
        Param::GRANT_TYPE,
        Param::CODE
    );
}