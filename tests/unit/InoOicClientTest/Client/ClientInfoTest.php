<?php

namespace InoOicClientTest\Client;

use InoOicClient\Client\ClientInfo;


class ClientInfoTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $clientId = '123';
        $redirectUri = 'https://client/redirect';
        $authenticationInfo = $this->getMock('InoOicClient\Client\AuthenticationInfo');
        
        $clientInfo = new ClientInfo($clientId, $redirectUri, $authenticationInfo);
        
        $this->assertSame($clientId, $clientInfo->getClientId());
        $this->assertSame($redirectUri, $clientInfo->getRedirectUri());
        $this->assertSame($authenticationInfo, $clientInfo->getAuthenticationInfo());
    }
}