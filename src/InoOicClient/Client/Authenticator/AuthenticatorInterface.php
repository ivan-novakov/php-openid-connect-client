<?php

namespace InoOicClient\Client\Authenticator;


interface AuthenticatorInterface
{


    public function configureHttpRequest(\Zend\Http\Request $httpRequest);
}