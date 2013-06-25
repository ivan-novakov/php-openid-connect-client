<?php

namespace InoOicClient\Client\Authenticator;


abstract class AbstractSecretAuthenticator extends AbstractAuthenticator
{

    const PARAM_CLIENT_SECRET = 'client_secret';


    /**
     * {@inhertidoc}
     * @see \InoOicClient\Client\Authenticator\AuthenticatorInterface::configureHttpRequest()
     */
    public function configureHttpRequest(\Zend\Http\Request $httpRequest)
    {
        $secret = $this->params->get(self::PARAM_CLIENT_SECRET);
        if (! $secret) {
            throw new Exception\MissingParameterException(self::PARAM_CLIENT_SECRET);
        }
        
        $this->setAuth($httpRequest, $this->clientId, $secret);
    }


    abstract public function setAuth(\Zend\Http\Request $httpRequest, $clientId, $clientSecret);
}