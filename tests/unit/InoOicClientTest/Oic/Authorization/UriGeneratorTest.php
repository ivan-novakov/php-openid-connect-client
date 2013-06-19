<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\UriGenerator;


class UriGeneratorTest extends \PHPUnit_Framework_TestCase
{

    protected $generator;


    public function setUp()
    {
        $this->generator = new UriGenerator();
    }


    /**
     *@dataProvider requestProvider
     */
    public function testCreateAuthorizationRequestUri($baseUri, $clientId, $redirectUri, $responseType, $scope, $state, 
        $expectedUri)
    {
        $clientInfo = $this->createClientInfoMock($clientId, $redirectUri);
        $serverInfo = $this->createServerInfoMock($baseUri);
        $request = $this->createRequestMock($responseType, $scope, $state, $clientInfo, $serverInfo);
        
        $resultUri = $this->generator->createAuthorizationRequestUri($request);
        
        $this->assertSame($expectedUri, $resultUri);
    }


    public function requestProvider()
    {
        return array(
            array(
                'https://oic.server.org/authorize',
                '123',
                'https://oic.client.org/redirect',
                array(
                    'code'
                ),
                array(
                    'openid'
                ),
                'a0a0a0a0',
                
                'https://oic.server.org/authorize?client_id=123&redirect_uri=' .
                     rawurlencode('https://oic.client.org/redirect') . '&response_type=code&scope=openid&state=a0a0a0a0'
            ),
            
            array(
                'https://oic.server.org/authorize',
                '123',
                'https://oic.client.org/redirect',
                array(
                    'code',
                    'token'
                ),
                array(
                    'openid',
                    'email'
                ),
                'a0a0a0a0',
                
                'https://oic.server.org/authorize?client_id=123&redirect_uri=' .
                 rawurlencode('https://oic.client.org/redirect') . '&response_type=' . rawurlencode('code token') . '&scope=' .
                 rawurlencode('openid email') . '&state=a0a0a0a0'
            )
        );
    }


    protected function createClientInfoMock($clientId, $redirectUri)
    {
        $clientInfo = $this->getMockBuilder('InoOicClient\Client\ClientInfo')
            ->disableOriginalConstructor()
            ->setMethods(array(
            'getClientId',
            'getRedirectUri'
        ))
            ->getMock();
        $clientInfo->expects($this->once())
            ->method('getClientId')
            ->will($this->returnValue($clientId));
        $clientInfo->expects($this->once())
            ->method('getRedirectUri')
            ->will($this->returnValue($redirectUri));
        return $clientInfo;
    }


    protected function createServerInfoMock($uri)
    {
        $serverInfo = $this->getMockBuilder('InoOicClient\Server\ServerInfo')
            ->disableOriginalConstructor()
            ->setMethods(array(
            'getAuthorizationEndpoint'
        ))
            ->getMock();
        $serverInfo->expects($this->once())
            ->method('getAuthorizationEndpoint')
            ->will($this->returnValue($uri));
        return $serverInfo;
    }


    protected function createRequestMock($responseType, $scope, $state, $clientInfo, $serverInfo)
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\Authorization\Request')
            ->disableOriginalConstructor()
            ->setMethods(
            array(
                'getResponseType',
                'getScope',
                'getState',
                'getServerInfo',
                'getClientInfo'
            ))
            ->getMock();
        
        $request->expects($this->any())
            ->method('getResponseType')
            ->will($this->returnValue($responseType));
        $request->expects($this->any())
            ->method('getScope')
            ->will($this->returnValue($scope));
        $request->expects($this->any())
            ->method('getState')
            ->will($this->returnValue($state));
        $request->expects($this->any())
            ->method('getClientInfo')
            ->will($this->returnValue($clientInfo));
        $request->expects($this->any())
            ->method('getServerInfo')
            ->will($this->returnValue($serverInfo));
        
        return $request;
    }
}