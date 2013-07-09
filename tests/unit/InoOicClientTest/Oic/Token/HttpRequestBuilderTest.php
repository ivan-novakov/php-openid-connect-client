<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\HttpRequestBuilder;
use InoOicClient\Oic\Token\Param;


class HttpRequestBuilderTest extends \PHPUnit_Framework_Testcase
{

    protected $builder;


    public function setUp()
    {
        $this->builder = new HttpRequestBuilder();
    }


    public function testGetClientAuthenticatorFactoryWithImplicitValue()
    {
        $factory = $this->builder->getClientAuthenticatorFactory();
        $this->assertInstanceOf('InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface', $factory);
    }


    public function testSetClientAuthenticatorFactory()
    {
        $factory = $this->createClientAuthenticatorFactoryMock();
        $this->builder->setClientAuthenticatorFactory($factory);
        $this->assertSame($factory, $this->builder->getClientAuthenticatorFactory());
    }


    public function testBuildHttpRequest()
    {
        $code = '123';
        $grantType = 'foo';
        $endpoint = 'https://oic/token';
        $clientId = 'abc';
        $redirectUri = 'https://client/redirect';
        $method = 'POST';
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'foo' => 'bar'
        );
        $builderOptions = array(
            'headers' => array(
                'foo' => 'bar'
            )
        );
        $this->builder->setOptions($builderOptions);
        
        $clientInfo = $this->createClientInfoMock($endpoint, $clientId, $redirectUri);
        $tokenRequest = $this->createTokenRequestMock($clientInfo, $code, $grantType);
        
        $authenticator = $this->createAuthenticatorMock();
        $authenticator->expects($this->once())
            ->method('configureHttpRequest');
        
        $authenticatorFactory = $this->createClientAuthenticatorFactoryMock();
        $authenticatorFactory->expects($this->once())
            ->method('createAuthenticator')
            ->with($clientInfo)
            ->will($this->returnValue($authenticator));
        
        $this->builder->setClientAuthenticatorFactory($authenticatorFactory);
        
        $httpRequest = $this->createHttpRequestMock($endpoint, $clientId, $redirectUri, $clientInfo, $code, $grantType, 
            $method, $headers);
        
        $builtHttpRequest = $this->builder->buildHttpRequest($tokenRequest, $httpRequest);
    }


    public function testBuildHttpRequestWithInvalidRequest()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\InvalidRequestException');
        
        $tokenRequest = $this->createTokenRequestMock();
        $this->builder->buildHttpRequest($tokenRequest);
    }
    
    /*
     * --------------
     */
    protected function createAuthenticatorMock()
    {
        $authenticator = $this->getMock('InoOicClient\Client\Authenticator\AuthenticatorInterface');
        return $authenticator;
    }


    protected function createClientAuthenticatorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface');
        return $factory;
    }


    protected function createClientInfoMock($endpoint = null, $clientId = null, $redirectUri = null)
    {
        $info = $this->getMockBuilder('InoOicClient\Client\ClientInfo')
            ->setMethods(
            array(
                'getTokenEndpoint',
                'getClientId',
                'getRedirectUri'
            ))
            ->getMock();
        
        if ($endpoint) {
            $info->expects($this->once())
                ->method('getTokenEndpoint')
                ->will($this->returnValue($endpoint));
        }
        
        if ($clientId) {
            $info->expects($this->once())
                ->method('getClientId')
                ->will($this->returnValue($clientId));
        }
        
        if ($redirectUri) {
            $info->expects($this->once())
                ->method('getRedirectUri')
                ->will($this->returnValue($redirectUri));
        }
        
        return $info;
    }


    protected function createTokenRequestMock($clientInfo = null, $code = null, $grantType = null)
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\Token\Request')
            ->setMethods(
            array(
                'getClientInfo',
                'getCode',
                'getGrantType'
            ))
            ->getMock();
        
        if ($clientInfo) {
            $request->expects($this->once())
                ->method('getClientInfo')
                ->will($this->returnValue($clientInfo));
        }
        
        if ($code) {
            $request->expects($this->once())
                ->method('getCode')
                ->will($this->returnValue($code));
        }
        
        if ($grantType) {
            $request->expects($this->once())
                ->method('getGrantType')
                ->will($this->returnValue($grantType));
        }
        
        return $request;
    }


    protected function createHttpRequestMock($endpoint, $clientId, $redirectUri, $clientInfo, $code, $grantType, $method, 
        $headersList)
    {
        $httpRequest = $this->getMock('Zend\Http\Request');
        $httpRequest->expects($this->once())
            ->method('setUri')
            ->with($endpoint);
        $httpRequest->expects($this->once())
            ->method('setMethod')
            ->with($method);
        
        $postData = array(
            Param::CLIENT_ID => $clientId,
            Param::REDIRECT_URI => $redirectUri,
            Param::GRANT_TYPE => $grantType,
            Param::CODE => $code
        );
        
        $postVars = $this->getMock('Zend\Stdlib\Parameters');
        $postVars->expects($this->once())
            ->method('fromArray')
            ->with($postData);
        
        $httpRequest->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($postVars));
        
        $headers = $this->getMock('Zend\Http\Headers');
        $headers->expects($this->once())
            ->method('addHeaders')
            ->with($headersList);
        
        $httpRequest->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($headers));
        
        return $httpRequest;
    }
}