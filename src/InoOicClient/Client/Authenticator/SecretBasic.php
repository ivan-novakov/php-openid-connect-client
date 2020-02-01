<?php

namespace InoOicClient\Client\Authenticator;


class SecretBasic extends AbstractSecretAuthenticator
{


    public function setAuth(\Laminas\Http\Request $httpRequest, $clientId, $clientSecret)
    {
        $httpRequest->getHeaders()->addHeaderLine('Authorization', 'Basic ' . $this->encode($clientId, $clientSecret));
    }


    public function encode($clientId, $clientSecret)
    {
        return base64_encode(urlencode($clientId) . ':' . $clientSecret);
    }
}