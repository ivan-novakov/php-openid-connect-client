<?php

namespace InoOicClientTest\Oic\UserInfo;

use InoOicClient\Oic\UserInfo\Dispatcher;


class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    protected $dispatcher;


    public function setUp()
    {
        $this->dispatcher = new Dispatcher();
    }


    public function testGetResponseHandlerWithImplicitValue()
    {
        $handler = $this->dispatcher->getResponseHandler();
        $this->assertInstanceOf('InoOicClient\Oic\UserInfo\ResponseHandler', $handler);
    }


    public function testSetResponseHandler()
    {
        $handler = $this->createResponseHandlerMock();
        $this->dispatcher->setResponseHandler($handler);
        $this->assertSame($handler, $this->dispatcher->getResponseHandler());
    }


    public function testGetHttpRequestBuilderWithImplicitValue()
    {
        $builder = $this->dispatcher->getHttpRequestBuilder();
        $this->assertInstanceOf('InoOicClient\Oic\UserInfo\HttpRequestBuilder', $builder);
    }


    public function testSetHttpRequestBuilder()
    {
        $builder = $this->createHttpRequestBuilderMock();
        $this->dispatcher->setHttpRequestBuilder($builder);
        $this->assertSame($builder, $this->dispatcher->getHttpRequestBuilder());
    }


    public function testSendUserInfoRequestWithBuildHttpRequestException()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\HttpRequestBuilderException');
        
        $request = $this->createUserInfoRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, null, true);
        
        $this->dispatcher->setHttpRequestBuilder($builder);
        
        $this->dispatcher->sendUserInfoRequest($request);
    }


    public function testSendUserInfoRequestWithResponseError()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\ErrorResponseException');
        
        $request = $this->createUserInfoRequestMock();
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        
        $error = $this->createErrorMock();
        $responseHandler = $this->createResponseHandlerMock($httpResponse, null, $error);
        
        $dispatcher = $this->createDispatcherMock($httpRequest, $httpResponse, $builder, $responseHandler);
        $dispatcher->sendUserInfoRequest($request);
    }


    public function testSendUserInfoRequestWithValidResponse()
    {
        $request = $this->createUserInfoRequestMock();
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $response = $this->getMock('InoOicClient\Oic\UserInfo\Response');
        
        $responseHandler = $this->createResponseHandlerMock($httpResponse, $response);
        
        $dispatcher = $this->createDispatcherMock($httpRequest, $httpResponse, $builder, $responseHandler);
        $this->assertSame($response, $dispatcher->sendUserInfoRequest($request));
    }
    
    /*
     * --------------------------
     */
    protected function createDispatcherMock($httpRequest, $httpResponse, $builder, $responseHandler)
    {
        $dispatcher = $this->getMockBuilder('InoOicClient\Oic\UserInfo\Dispatcher')
            ->setMethods(array(
            'sendHttpRequest'
        ))
            ->getMock();
        
        $dispatcher->expects($this->once())
            ->method('sendHttpRequest')
            ->with($httpRequest)
            ->will($this->returnValue($httpResponse));
        
        $dispatcher->setHttpRequestBuilder($builder);
        
        $dispatcher->setResponseHandler($responseHandler);
        
        return $dispatcher;
    }


    protected function createResponseHandlerMock($httpResponse = null, $response = null, $error = null)
    {
        $handler = $this->getMock('InoOicClient\Oic\UserInfo\ResponseHandler');
        
        if ($httpResponse) {
            $handler->expects($this->once())
                ->method('handleResponse')
                ->with($httpResponse);
            
            if ($response) {
                $handler->expects($this->once())
                    ->method('isError')
                    ->will($this->returnValue(false));
                $handler->expects($this->once())
                    ->method('getResponse')
                    ->will($this->returnValue($response));
            } elseif ($error) {
                $handler->expects($this->once())
                    ->method('isError')
                    ->will($this->returnValue(true));
                $handler->expects($this->once())
                    ->method('getError')
                    ->will($this->returnValue($error));
            }
        }
        return $handler;
    }


    protected function createHttpClientMock($httpRequest = null, $httpRespone = null, $throwException = false)
    {
        $client = $this->getMock('Zend\Http\Client');
        
        return $client;
    }


    protected function createHttpRequestMock()
    {
        $httpRequest = $this->getMock('Zend\Http\Request');
        return $httpRequest;
    }


    protected function createHttpResponseMock()
    {
        $httpResponse = $this->getMock('Zend\Http\Response');
        return $httpResponse;
    }


    protected function createUserInfoRequestMock()
    {
        $request = $this->getMock('InoOicClient\Oic\UserInfo\Request');
        return $request;
    }


    protected function createHttpRequestBuilderMock($request = null, $httpRequest = null, $throwException = false)
    {
        $builder = $this->getMock('InoOicClient\Oic\UserInfo\HttpRequestBuilder');
        
        if ($throwException) {
            $builder->expects($this->once())
                ->method('buildHttpRequest')
                ->with($request)
                ->will($this->throwException(new \Exception()));
        } elseif ($httpRequest) {
            $builder->expects($this->once())
                ->method('buildHttpRequest')
                ->with($request)
                ->will($this->returnValue($httpRequest));
        }
        
        return $builder;
    }


    public function createErrorMock()
    {
        $error = $this->getMock('InoOicClient\Oic\Error');
        return $error;
    }
}