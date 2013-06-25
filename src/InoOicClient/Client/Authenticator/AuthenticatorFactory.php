<?php

namespace InoOicClient\Client\Authenticator;

use InoOicClient\Client\AuthenticationInfo;


class AuthenticatorFactory implements AuthenticatorFactoryInterface
{


    /**
     * {@inhertidoc}
     * @see \InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface::createAuthenticator()
     */
    public function createAuthenticator(AuthenticationInfo $authenticationInfo)
    {
        $authenticator = null;
        
        $authMethod = $authenticationInfo->getMethod();
        $authParams = $authenticationInfo->getParams();
        
        switch ($authMethod) {
            
            case AuthenticationInfo::METHOD_SECRET_BASIC:
                $authenticator = $this->createSecretBasic($authParams);
                break;
            
            case AuthenticationInfo::METHOD_SECRET_POST:
                $authenticator = $this->createSecretPost($authParams);
                break;
            
            default:
                throw new Exception\UnsupportedAuthenticationMethodException(
                    sprintf("Unsupported client authentication method '%s'", $authMethod));
        }
        
        return $authenticator;
    }


    protected function createSecretBasic(array $params)
    {
        return new SecretBasic($params);
    }


    protected function createSecretPost(array $params)
    {
        return new SecretPost($params);
    }
}