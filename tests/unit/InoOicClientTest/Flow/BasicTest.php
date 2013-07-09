<?php

namespace InoOicClientTest\Flow;

use InoOicClient\Flow\Basic;


class BasicTest extends \PHPUnit_Framework_Testcase
{

    protected $flow;


    public function setUp()
    {
        $this->flow = new Basic();
    }


    public function testGetAuthorizationRequestUri()
    {
        $scope = 'openid';
        $responseType = 'code';
        $request = $this->getMockBuilder('InoOicClient\Oic\Authorization\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $uri = 'https://server/authorize?foo';
        
        $flow = $this->getMockBuilder('InoOicClient\Flow\Basic')
            ->setMethods(array(
            'createAuthorizationRequest'
        ))
            ->getMock();
        $flow->expects($this->once())
            ->method('createAuthorizationRequest')
            ->with($scope, $responseType)
            ->will($this->returnValue($request));
        
        $dispatcher = $this->getMock('InoOicClient\Oic\Authorization\Dispatcher');
        $dispatcher->expects($this->once())
            ->method('createAuthorizationRequestUri')
            ->with($request)
            ->will($this->returnValue($uri));
        $flow->setAuthorizationDispatcher($dispatcher);
        
        $this->assertSame($uri, $flow->getAuthorizationRequestUri());
    }


    public function testGetAuthorizationCode()
    {
        $code = '123';
        $response = $this->getMockBuilder('InoOicClient\Oic\Authorization\Response')
            ->setMethods(array(
            'getCode'
        ))
            ->getMock();
        $response->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($code));
        
        $dispatcher = $this->getMock('InoOicClient\Oic\Authorization\Dispatcher');
        $dispatcher->expects($this->once())
            ->method('getAuthorizationResponse')
            ->will($this->returnValue($response));
        $this->flow->setAuthorizationDispatcher($dispatcher);
        
        $this->assertSame($code, $this->flow->getAuthorizationCode());
    }


    public function testGetAccessToken()
    {
        $code = 'abc';
        $token = '123';
        
        $request = $this->getMockBuilder('InoOicClient\Oic\Token\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $flow = $this->getMockBuilder('InoOicClient\Flow\Basic')
            ->setMethods(array(
            'createTokenRequest'
        ))
            ->getMock();
        $flow->expects($this->once())
            ->method('createTokenRequest')
            ->with($code)
            ->will($this->returnValue($request));
        
        $response = $this->getMockBuilder('InoOicClient\Oic\Token\Response')
            ->setMethods(array(
            'getAccessToken'
        ))
            ->getMock();
        $response->expects($this->once())
            ->method('getAccessToken')
            ->will($this->returnValue($token));
        
        $dispatcher = $this->getMock('InoOicClient\Oic\Token\Dispatcher');
        $dispatcher->expects($this->once())
            ->method('sendTokenRequest')
            ->with($request)
            ->will($this->returnValue($response));
        $flow->setTokenDispatcher($dispatcher);
        
        $this->assertSame($token, $flow->getAccessToken($code));
    }


    public function testGetUserInfo()
    {
        $token = '123';
        $claims = array(
            'foo' => 'bar'
        );
        
        $request = $this->getMockBuilder('InoOicClient\Oic\UserInfo\Request')
            ->disableOriginalConstructor()
            ->getMock();
        
        $flow = $this->getMockBuilder('InoOicClient\Flow\Basic')
            ->setMethods(array(
            'createUserInfoRequest'
        ))
            ->getMock();
        $flow->expects($this->once())
            ->method('createUserInfoRequest')
            ->with($token)
            ->will($this->returnValue($request));
        
        $response = $this->getMockBuilder('InoOicClient\Oic\UserInfo\Response')
            ->setMethods(array(
            'getClaims'
        ))
            ->getMock();
        $response->expects($this->once())
            ->method('getClaims')
            ->will($this->returnValue($claims));
        
        $dispatcher = $this->getMock('InoOicClient\Oic\UserInfo\Dispatcher');
        $dispatcher->expects($this->once())
            ->method('sendUserInfoRequest')
            ->with($request)
            ->will($this->returnValue($response));
        $flow->setUserInfoDispatcher($dispatcher);
        
        $this->assertSame($claims, $flow->getUserInfo($token));
    }


    public function testProcess()
    {
        $code = '123';
        $token = 'abc';
        $claims = array(
            'foo' => 'bar'
        );
        
        $flow = $this->getMockBuilder('InoOicClient\Flow\Basic')
            ->setMethods(
            array(
                'getAuthorizationCode',
                'getAccessToken',
                'getUserInfo'
            ))
            ->getMock();
        
        $flow->expects($this->once())
            ->method('getAuthorizationCode')
            ->will($this->returnValue($code));
        $flow->expects($this->once())
            ->method('getAccessToken')
            ->with($code)
            ->will($this->returnValue($token));
        $flow->expects($this->once())
            ->method('getUserInfo')
            ->with($token)
            ->will($this->returnValue($claims));
        
        $this->assertSame($claims, $flow->process());
    }


    public function testProcessWithAuthorizationException()
    {
        $this->setExpectedException('InoOicClient\Flow\Exception\AuthorizationException');
        
        $flow = $this->getMockBuilder('InoOicClient\Flow\Basic')
            ->setMethods(array(
            'getAuthorizationCode'
        ))
            ->getMock();
        
        $flow->expects($this->once())
            ->method('getAuthorizationCode')
            ->will($this->throwException(new \Exception()));
        
        $flow->process();
    }


    public function testProcessWithTokenRequestException()
    {
        $this->setExpectedException('InoOicClient\Flow\Exception\TokenRequestException');
        
        $code = '123';
        
        $flow = $this->getMockBuilder('InoOicClient\Flow\Basic')
            ->setMethods(array(
            'getAuthorizationCode',
            'getAccessToken'
        ))
            ->getMock();
        
        $flow->expects($this->once())
            ->method('getAuthorizationCode')
            ->will($this->returnValue($code));
        
        $flow->expects($this->once())
            ->method('getAccessToken')
            ->with($code)
            ->will($this->throwException(new \Exception()));
        
        $flow->process();
    }


    public function testProcessWithUserInfoRequestException()
    {
        $this->setExpectedException('InoOicClient\Flow\Exception\UserInfoRequestException');
        
        $code = '123';
        $token = 'abc';
        
        $flow = $this->getMockBuilder('InoOicClient\Flow\Basic')
            ->setMethods(
            array(
                'getAuthorizationCode',
                'getAccessToken',
                'getUserInfo'
            ))
            ->getMock();
        
        $flow->expects($this->once())
            ->method('getAuthorizationCode')
            ->will($this->returnValue($code));
        $flow->expects($this->once())
            ->method('getAccessToken')
            ->with($code)
            ->will($this->returnValue($token));
        $flow->expects($this->once())
            ->method('getUserInfo')
            ->with($token)
            ->will($this->throwException(new \Exception()));
        
        $flow->process();
    }


    public function testCreateAuthorizationRequest()
    {
        $scope = array(
            'openid'
        );
        $responseType = array(
            'code'
        );
        $clientInfo = $this->getMock('InoOicClient\Client\ClientInfo');
        $this->flow->setClientInfo($clientInfo);
        
        $request = $this->flow->createAuthorizationRequest($scope, $responseType);
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\Request', $request);
        $this->assertSame($scope, $request->getScope());
        $this->assertSame($responseType, $request->getResponseType());
        $this->assertSame($clientInfo, $request->getClientInfo());
    }


    public function testCreateTokenRequest()
    {
        $code = '123';
        $clientInfo = $this->getMock('InoOicClient\Client\ClientInfo');
        $this->flow->setClientInfo($clientInfo);
        
        $request = $this->flow->createTokenRequest($code);
        $this->assertInstanceOf('InoOicClient\Oic\Token\Request', $request);
        $this->assertSame($code, $request->getCode());
        $this->assertSame($clientInfo, $request->getClientInfo());
    }


    public function testCreateUserInfoRequest()
    {
        $token = 'abc';
        $clientInfo = $this->getMock('InoOicClient\Client\ClientInfo');
        $this->flow->setClientInfo($clientInfo);
        
        $request = $this->flow->createUserInfoRequest($token);
        $this->assertInstanceOf('InoOicClient\Oic\UserInfo\Request', $request);
        $this->assertSame($token, $request->getAccessToken());
        $this->assertSame($clientInfo, $request->getClientInfo());
    }
}