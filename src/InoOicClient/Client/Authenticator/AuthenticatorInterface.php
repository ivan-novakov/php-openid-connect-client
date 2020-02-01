<?php

namespace InoOicClient\Client\Authenticator;


interface AuthenticatorInterface
{


    public function configureHttpRequest(\Laminas\Http\Request $httpRequest);
}