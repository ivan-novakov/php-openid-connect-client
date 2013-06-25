<?php

namespace InoOicClient\Client\Authenticator;

use InoOicClient\Client\ClientInfo;
use InoOicClient\Client\AuthenticationInfo;


class AuthenticatorFactory implements AuthenticatorFactoryInterface
{


    /**
     * {@inhertidoc}
     * @see \InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface::createAuthenticator()
     */
    public function createAuthenticator(ClientInfo $clientInfo)
    {
        $authenticator = null;
        
        $authenticationInfo = $clientInfo->getAuthenticationInfo();
        $clientId = $clientInfo->getClientId();
        
        $authMethod = $authenticationInfo->getMethod();
        $authParams = $authenticationInfo->getParams();
        
        switch ($authMethod) {
            
            case AuthenticationInfo::METHOD_SECRET_BASIC:
                $authenticator = $this->createSecretBasic($clientId, $authParams);
                break;
            
            case AuthenticationInfo::METHOD_SECRET_POST:
                $authenticator = $this->createSecretPost($clientId, $authParams);
                break;
            
            default:
                throw new Exception\UnsupportedAuthenticationMethodException(
                    sprintf("Unsupported client authentication method '%s'", $authMethod));
        }
        
        return $authenticator;
    }


    protected function createSecretBasic($clientId, array $params)
    {
        return new SecretBasic($clientId, $params);
    }


    protected function createSecretPost($clientId, array $params)
    {
        return new SecretPost($clientId, $params);
    }
}