<?php

namespace InoOicClient\Oic;

use InoOicClient\Entity\AbstractEntity;


/**
 * Generic error response.
 * 
 * @method void setCode(string $code)
 * @method void setDescription(string $description)
 * @method void setUri(string $uri)
 * 
 * @method string getCode()
 * @method string getDescription()
 * @method string getUri()
 */
class Error extends AbstractEntity
{

    protected $allowedProperties = array(
        'code',
        'description',
        'uri'
    );
}