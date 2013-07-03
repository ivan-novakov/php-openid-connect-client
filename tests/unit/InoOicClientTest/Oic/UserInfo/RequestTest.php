<?php

namespace InoOicClientTest\Oic\UserInfo;

use InoOicClient\Oic\UserInfo\Request;


class RequestTest extends \PHPUnit_Framework_TestCase
{


    public function testGettersAndSetters()
    {
        $token = 'abc';
        $clientInfo = $this->getMock('InoOicClient\Client\ClientInfo');
        
        $request = new Request();
        $request->setAccessToken($token);
        $request->setClientInfo($clientInfo);
        
        $this->assertSame($token, $request->getAccessToken());
        $this->assertSame($clientInfo, $request->getClientInfo());
    }
}