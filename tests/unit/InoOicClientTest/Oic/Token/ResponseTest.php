<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\Response;


class ResponseTest extends \PHPUnit_Framework_Testcase
{


    public function testGettersAndSetters()
    {
        $accessToken = '1234';
        $tokenType = 'bearer';
        $refreshToken = '5678';
        $expiresIn = 1800;
        $scope = 'foo bar';
        $idToken = 'abcde';
        
        $response = new Response();
        $response->setAccessToken($accessToken);
        $response->setTokenType($tokenType);
        $response->setRefreshToken($refreshToken);
        $response->setExpiresIn($expiresIn);
        $response->setScope($scope);
        $response->setIdToken($idToken);
        
        $this->assertSame($accessToken, $response->getAccessToken());
        $this->assertSame($tokenType, $response->getTokenType());
        $this->assertSame($refreshToken, $response->getRefreshToken());
        $this->assertSame($expiresIn, $response->getExpiresIn());
        $this->assertSame($scope, $response->getScope());
        $this->assertSame($idToken, $response->getIdToken());
    }
}