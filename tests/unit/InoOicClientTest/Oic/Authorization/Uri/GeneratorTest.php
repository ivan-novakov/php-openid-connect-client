<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\Uri\Generator;


class GeneratorTest extends \PHPUnit_Framework_TestCase
{

    protected $generator;


    public function setUp()
    {
        $this->generator = new Generator();
    }


    /**
     *@dataProvider requestProvider
     */
    public function testCreateAuthorizationRequestUri($baseUri, $clientId, $redirectUri, $responseType, $scope, $state, 
        $expectedUri, $endpointException = false, $fieldException = false)
    {
        if ($endpointException) {
            $this->setExpectedException('InoOicClient\Oic\Authorization\Uri\Exception\MissingEndpointException');
        }
        
        if ($fieldException) {
            $this->setExpectedException('InoOicClient\Oic\Authorization\Uri\Exception\MissingFieldException');
        }
        
        $clientInfo = $this->createClientInfoMock($clientId, $redirectUri, $baseUri);
        $request = $this->createRequestMock($responseType, $scope, $state, $clientInfo);
        
        $resultUri = $this->generator->createAuthorizationRequestUri($request);
        
        $this->assertSame($expectedUri, $resultUri);
    }


    public function requestProvider()
    {
        return array(
            // with single scope and responseType
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
            
            // with multiple scopes and responseTypes
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
                 rawurlencode('https://oic.client.org/redirect') . '&response_type=' . rawurlencode('code token') .
                 '&scope=' . rawurlencode('openid email') . '&state=a0a0a0a0'
            ),
            
            // with missing endpoint
            array(
                null,
                '123',
                'https://oic.client.org/redirect',
                array(
                    'code'
                ),
                array(
                    'openid'
                ),
                'a0a0a0a0',
                
                null,
                
                true
            ),
            
            // with missing clientId
            array(
                'https://oic.server.org/authorize',
                null,
                'https://oic.client.org/redirect',
                array(
                    'code'
                ),
                array(
                    'openid'
                ),
                'a0a0a0a0',
                
                null,
                
                false,
                
                true
            ),
            
            // with missing redirectUri
            array(
                'https://oic.server.org/authorize',
                '123',
                null,
                array(
                    'code'
                ),
                array(
                    'openid'
                ),
                'a0a0a0a0',
                
                null,
                
                false,
                
                true
            ),
            
            // with missing responseType
            array(
                'https://oic.server.org/authorize',
                '123',
                'https://oic.client.org/redirect',
                array(),
                array(
                    'openid'
                ),
                'a0a0a0a0',
                
                null,
                
                false,
                
                true
            ),
            
            // with missing scope
            array(
                'https://oic.server.org/authorize',
                '123',
                'https://oic.client.org/redirect',
                array(
                    'code'
                ),
                array(),
                'a0a0a0a0',
                
                null,
                
                false,
                
                true
            )
        );
    }


    protected function createClientInfoMock($clientId, $redirectUri, $endpointUri)
    {
        $clientInfo = $this->getMockBuilder('InoOicClient\Client\ClientInfo')
            ->disableOriginalConstructor()
            ->setMethods(
            array(
                'getClientId',
                'getRedirectUri',
                'getAuthorizationEndpoint'
            ))
            ->getMock();
        $clientInfo->expects($this->any())
            ->method('getClientId')
            ->will($this->returnValue($clientId));
        $clientInfo->expects($this->any())
            ->method('getRedirectUri')
            ->will($this->returnValue($redirectUri));
        $clientInfo->expects($this->once())
            ->method('getAuthorizationEndpoint')
            ->will($this->returnValue($endpointUri));
        return $clientInfo;
    }


    protected function createRequestMock($responseType, $scope, $state, $clientInfo)
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\Authorization\Request')
            ->disableOriginalConstructor()
            ->setMethods(
            array(
                'getResponseType',
                'getScope',
                'getState',
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
        
        return $request;
    }
}