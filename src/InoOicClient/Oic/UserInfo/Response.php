<?php

namespace InoOicClient\Oic\UserInfo;

use InoOicClient\Entity\AbstractEntity;


/**
 * User info response.
 * 
 * @method void setClaims(array $claims)
 * @method array getClaims()
 */
class Response extends AbstractEntity
{

    const PROP_CLAIMS = 'claims';

    protected $allowedProperties = array(
        self::PROP_CLAIMS
    );
}