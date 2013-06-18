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


    /**
     * Constructor.
     * 
     * @param string $code
     * @param string $state
     */
    public function __construct($code, $state = null)
    {
        $this->setCode($code);
        $this->setState($state);
    }
}