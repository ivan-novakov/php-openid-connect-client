<?php

namespace InoOicClient\Oic\Authorization;

use InoOicClient\Entity\AbstractEntity;


/**
 * Authorization response.
 * 
 * @method void setCode(string $code)
 * @method void setState(string $state)
 * 
 * @method string getCode()
 * @method string getState()
 */
class Response extends AbstractEntity
{

    protected $allowedProperties = array(
        'code',
        'state'
    );
}