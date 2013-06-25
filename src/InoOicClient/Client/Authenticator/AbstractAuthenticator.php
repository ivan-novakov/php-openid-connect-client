<?php

namespace InoOicClient\Client\Authenticator;

use Zend\Stdlib\Parameters;


abstract class AbstractAuthenticator implements AuthenticatorInterface
{

    /**
     * The client ID registered at the IdP.
     * @var string
     */
    protected $clientId;

    /**
     * Authenticator parameters.
     * @var array
     */
    protected $params;


    /**
     * Constructor.
     * 
     * @param string $clientId
     * @param array $params
     */
    public function __construct($clientId, array $params = array())
    {
        $this->clientId = $clientId;
        $this->params = new Parameters($params);
    }
}