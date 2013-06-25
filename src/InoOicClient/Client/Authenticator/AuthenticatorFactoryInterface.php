<?php

namespace InoOicClient\Client\Authenticator;

use InoOicClient\Client\AuthenticationInfo;


interface AuthenticatorFactoryInterface
{


    /**
     * Creates the appropriate client authentiactor based on the provided authentication info.
     * 
     * @param AuthenticationInfo $authenticationInfo
     * @reteurn AuthenticatorInterface
     */
    public function createAuthenticator(AuthenticationInfo $authenticationInfo);
}
