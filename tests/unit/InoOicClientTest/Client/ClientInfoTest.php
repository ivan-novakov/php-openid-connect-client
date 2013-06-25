<?php

namespace InoOicClientTest\Client;

use InoOicClient\Client\ClientInfo;


class ClientInfoTest extends \PHPUnit_Framework_TestCase
{


    public function testSettersAndGetters()
    {
        $clientId = '123';
        $redirectUri = 'https://client/redirect';
        $authenticationInfo = $this->getMock('InoOicClient\Client\AuthenticationInfo');
        $name = 'test client';
        $description = 'test client desc';
        $authEndpoint = 'https://server/auth';
        $tokenEndpoint = 'https://server/token';
        $userInfoEndpoint = 'https://server/userinfo';
        
        $clientInfo = new ClientInfo();
        
        $clientInfo->setClientId($clientId);
        $clientInfo->setRedirectUri($redirectUri);
        $clientInfo->setAuthenticationInfo($authenticationInfo);
        $clientInfo->setName($name);
        $clientInfo->setDescription($description);
        $clientInfo->setAuthorizationEndpoint($authEndpoint);
        $clientInfo->setTokenEndpoint($tokenEndpoint);
        $clientInfo->setUserInfoEndpoint($userInfoEndpoint);
        
        $this->assertSame($clientId, $clientInfo->getClientId());
        $this->assertSame($redirectUri, $clientInfo->getRedirectUri());
        $this->assertSame($authenticationInfo, $clientInfo->getAuthenticationInfo());
        $this->assertSame($name, $clientInfo->getName());
        $this->assertSame($description, $clientInfo->getDescription());
        $this->assertSame($authEndpoint, $clientInfo->getAuthorizationEndpoint());
        $this->assertSame($tokenEndpoint, $clientInfo->getTokenEndpoint());
        $this->assertSame($userInfoEndpoint, $clientInfo->getUserInfoEndpoint());
    }


    public function testFromArray()
    {
        $clientId = '123';
        $redirectUri = 'https://client/redirect';
        $method = 'secret';
        $params = array(
            'password' => 'abc'
        );
        $authenticationInfo = array(
            'method' => $method,
            'params' => $params
        );
        
        $properties = array(
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'authentication_info' => $authenticationInfo
        );
        
        $info = new ClientInfo();
        $info->fromArray($properties);
        
        $this->assertSame($clientId, $info->getClientId());
        $this->assertSame($redirectUri, $info->getRedirectUri());
        
        $authenticationInfo = $info->getAuthenticationInfo();
        $this->assertInstanceOf('InoOicClient\Client\AuthenticationInfo', $authenticationInfo);
        $this->assertSame($method, $authenticationInfo->getMethod());
        $this->assertSame($params, $authenticationInfo->getParams());
    }
}