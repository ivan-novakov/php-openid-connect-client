<?php

namespace InoOicClientTest\Oic\UserInfo;

use InoOicClient\Oic\UserInfo\HttpRequestBuilder;


class HttpRequestBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $builder;


    public function setUp()
    {
        $this->builder = new HttpRequestBuilder();
    }


    public function testBuildHttpRequestWithoutClientInfo()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\InvalidRequestException');
        
        $request = $this->createRequestMock();
        $httpRequest = $this->createHttpRequest();
        $this->builder->buildHttpRequest($request, $httpRequest);
    }


    public function testBuildHttpRequest()
    {
        $token = 'abc';
        $headerName = 'Authorization';
        $headerValue = "Bearer $token";
        $endpoint = 'http://server/userinfo';
        
        $headersList = array(
            $headerName => $headerValue
        );
        
        $headers = $this->getMock('Zend\Http\Headers');
        $headers->expects($this->once())
            ->method('addHeaders')
            ->with($headersList);
        
        $clientInfo = $this->createClientInfoMock($endpoint);
        
        $httpRequest = $this->createHttpRequest('GET', $headers, $endpoint);

        $request = $this->createRequestMock($token, $clientInfo);
        
        $this->builder->buildHttpRequest($request, $httpRequest);
    }
    
    /*
     * ----------------------------------------
     */
    protected function createHttpRequest($method = null, $headers = null, $endpoint = null)
    {
        $httpRequest = $this->getMock('Zend\Http\Request');
        
        if ($method) {
            $httpRequest->expects($this->once())
                ->method('setMethod')
                ->with('GET');
        }
        
        if ($headers) {
            $httpRequest->expects($this->once())
                ->method('getHeaders')
                ->will($this->returnValue($headers));
        }
        
        if ($endpoint) {
            $httpRequest->expects($this->once())
                ->method('setUri')
                ->with($endpoint);
        }
        
        return $httpRequest;
    }


    protected function createRequestMock($token = null, $clientInfo = null)
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\UserInfo\Request')
            ->setMethods(array(
            'getAccessToken',
            'getClientInfo'
        ))
            ->getMock();
        
        if ($token) {
            $request->expects($this->once())
                ->method('getAccessToken')
                ->will($this->returnValue($token));
        }
        
        if ($clientInfo) {
            $request->expects($this->once())
                ->method('getClientInfo')
                ->will($this->returnValue($clientInfo));
        }
        
        return $request;
    }


    protected function createClientInfoMock($userInfoEndpoint)
    {
        $info = $this->getMockBuilder('InoOicClient\Client\ClientInfo')
            ->setMethods(array(
            'getUserInfoEndpoint'
        ))
            ->getMock();
        
        $info->expects($this->once())
            ->method('getUserInfoEndpoint')
            ->will($this->returnValue($userInfoEndpoint));
        
        return $info;
    }
}