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

    const METHOD = 'method';

    const PARAMS = 'params';

    protected $allowedProperties = array(
        self::METHOD,
        self::PARAMS
    );
}