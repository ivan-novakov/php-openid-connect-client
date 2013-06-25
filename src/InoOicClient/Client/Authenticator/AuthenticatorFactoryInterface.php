<?php

namespace InoOicClient\Client\Authenticator;

use InoOicClient\Client\ClientInfo;


interface AuthenticatorFactoryInterface
{


    /**
     * Creates the appropriate client authentiactor based on the provided authentication info.
     * 
     * @param ClientInfo $clientInfo
     * @reteurn AuthenticatorInterface
     */
    public function createAuthenticator(ClientInfo $clientInfo);
}
