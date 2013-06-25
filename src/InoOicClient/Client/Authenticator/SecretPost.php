<?php

namespace InoOicClient\Client\Authenticator;

use InoOicClient\Oic\Token\Param;


class SecretPost extends AbstractSecretAuthenticator
{


    public function setAuth(\Zend\Http\Request $httpRequest, $clientId, $clientSecret)
    {
        $postParams = $httpRequest->getPost();
        $postParams->set(Param::CLIENT_ID, $clientId);
        $postParams->set(Param::CLIENT_SECRET, $clientSecret);
    }
}