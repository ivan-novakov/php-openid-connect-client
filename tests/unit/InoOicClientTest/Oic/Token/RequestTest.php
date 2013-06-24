<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\Request;


class RequestTest extends \PHPUnit_Framework_Testcase
{


    public function testSettersAndGetters()
    {
        $serverInfo = $this->getMock('InoOicClient\Server\ServerInfo');
        $clientInfo = $this->getMock('InoOicClient\Client\ClientInfo');
        $grantType = 'authorization_code';
        $code = '1234';
        
        $request = new Request();
        $request->setServerInfo($serverInfo);
        $request->setClientInfo($clientInfo);
        $request->setGrantType($grantType);
        $request->setCode($code);
        
        $this->assertSame($serverInfo, $request->getServerInfo());
        $this->assertSame($clientInfo, $request->getClientInfo());
        $this->assertSame($grantType, $request->getGrantType());
        $this->assertSame($code, $request->getCode());
    }
}