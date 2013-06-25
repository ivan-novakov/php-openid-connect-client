<?php

namespace InoOicClient\Client\Authenticator;

use Zend\Stdlib\Parameters;


abstract class AbstractAuthenticator implements AuthenticatorInterface
{

    protected $params;


    public function __construct(array $params = array())
    {
        $this->params = new Parameters($params);
    }
}