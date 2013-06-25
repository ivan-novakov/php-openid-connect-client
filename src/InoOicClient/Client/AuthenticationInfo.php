<?php

namespace InoOicClient\Client;

use InoOicClient\Entity\AbstractEntity;


/**
 * Client authentication info.
 * 
 * @method void setMethod(string $method)
 * @method void setParams(array $params)
 * 
 * @method string getMethod()
 * @method array getParams()
 */
class AuthenticationInfo extends AbstractEntity
{

    const METHOD_SECRET_BASIC = 'client_secret_basic';

    const METHOD_SECRET_POST = 'client_secret_post';

    const PROP_METHOD = 'method';

    const PROP_PARAMS = 'params';

    protected $allowedProperties = array(
        self::PROP_METHOD,
        self::PROP_PARAMS
    );
}