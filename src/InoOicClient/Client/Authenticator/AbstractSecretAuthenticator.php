<?php

namespace InoOicClient\Client\Authenticator;


abstract class AbstractSecretAuthenticator extends AbstractAuthenticator
{

    const PARAM_CLIENT_ID = 'client_id';

    const PARAM_SECRET = 'secret';


    /**
     * {@inhertidoc}
     * @see \InoOicClient\Client\Authenticator\AuthenticatorInterface::configureHttpRequest()
     */
    public function configureHttpRequest(\Zend\Http\Request $httpRequest)
    {
        $clientId = $this->params->get(self::PARAM_CLIENT_ID);
        if (! $clientId) {
            throw new Exception\MissingParameterException(self::PARAM_CLIENT_ID);
        }
        
        $secret = $this->params->get(self::PARAM_SECRET);
        if (! $secret) {
            throw new Exception\MissingParameterException(self::PARAM_SECRET);
        }
        
        $this->setAuth($httpRequest, $clientId, $secret);
    }


    abstract public function setAuth(\Zend\Http\Request $httpRequest, $clientId, $clientSecret);
}