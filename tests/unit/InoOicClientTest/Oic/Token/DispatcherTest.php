<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\Dispatcher;
use InoOicClient\Oic\Token\Param;


class DispatcherTest extends \PHPUnit_Framework_Testcase
{

    protected $dispatcher;


    public function setUp()
    {
        $this->dispatcher = new Dispatcher();
    }


    public function testGetHttpRequestBuilderWithImplicitValue()
    {
        $httpOptions = array(
            'foo' => 'bar'
        );
        
        $this->dispatcher->setOptions(array(
            Dispatcher::OPT_HTTP_OPTIONS => $httpOptions
        ));
        $builder = $this->dispatcher->getHttpRequestBuilder();
        $this->assertInstanceOf('InoOicClient\Oic\Token\HttpRequestBuilder', $builder);
        $this->assertSame($httpOptions, $builder->getOptions()->toArray());
    }


    public function testSetHttpRequestBuilder()
    {
        $builder = $this->createHttpRequestBuilderMock();
        $this->dispatcher->setHttpRequestBuilder($builder);
        $this->assertSame($builder, $this->dispatcher->getHttpRequestBuilder());
    }


    public function testGetResponseHandlerWithImplicitValue()
    {
        $handler = $this->dispatcher->getResponseHandler();
        $this->assertInstanceOf('InoOicClient\Oic\Token\ResponseHandler', $handler);
    }


    public function testSetResponseHandler()
    {
        $handler = $this->createResponseHandlerMock();
        $this->dispatcher->setResponseHandler($handler);
        $this->assertSame($handler, $this->dispatcher->getResponseHandler());
    }


    public function testSendTokenRequestWithHttpRequestBuilderException()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\HttpRequestBuilderException');
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, null, true);
        $this->dispatcher->setHttpRequestBuilder($builder);
        $this->dispatcher->sendTokenRequest($request);
    }


    public function testSendTokenRequestWithResponseError()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\ErrorResponseException');
        
        $request = $this->createTokenRequestMock();
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        
        $error = $this->createErrorMock();
        $responseHandler = $this->createResponseHandlerMock($httpResponse, null, $error);
        
        $dispatcher = $this->createDispatcherMock($httpRequest, $httpResponse, $builder, $responseHandler);
        $dispatcher->sendTokenRequest($request);
    }


    public function testSendTokenRequestWithValidResponse()
    {
        $request = $this->createTokenRequestMock();
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $response = $this->getMock('InoOicClient\Oic\Token\Response');
        
        $responseHandler = $this->createResponseHandlerMock($httpResponse, $response);
        
        $dispatcher = $this->createDispatcherMock($httpRequest, $httpResponse, $builder, $responseHandler);
        $this->assertSame($response, $dispatcher->sendTokenRequest($request));
    }
    
    /*
     * -------
     */
    protected function createDispatcherMock($httpRequest, $httpResponse, $builder, $responseHandler)
    {
        $dispatcher = $this->getMockBuilder('InoOicClient\Oic\Token\Dispatcher')
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


    protected function createHttpRequestBuilderMock($tokenRequest = null, $httpRequest = null, $throwException = false)
    {
        $builder = $this->getMock('InoOicClient\Oic\Token\HttpRequestBuilder');
        
        if ($throwException) {
            $builder->expects($this->once())
                ->method('buildHttpRequest')
                ->with($tokenRequest)
                ->will($this->throwException(new \Exception()));
        } elseif ($tokenRequest && $httpRequest) {
            $builder->expects($this->once())
                ->method('buildHttpRequest')
                ->with($tokenRequest)
                ->will($this->returnValue($httpRequest));
        }
        
        return $builder;
    }


    protected function createHttpRequestMock()
    {
        $httpRequest = $this->getMock('Zend\Http\Request');
        return $httpRequest;
    }


    protected function createHttpResponseMock($content = null, $status = null, $error = false)
    {
        $httpResponse = $this->getMock('Zend\Http\Response');
        
        if ($content) {
            $httpResponse->expects($this->once())
                ->method('getContent')
                ->will($this->returnValue($content));
        }
        
        if ($error) {
            $httpResponse->expects($this->once())
                ->method('isSuccess')
                ->will($this->returnValue(false));
        } else {
            $httpResponse->expects($this->any())
                ->method('isSuccess')
                ->will($this->returnValue(true));
        }
        
        if ($status) {
            $httpResponse->expects($this->once())
                ->method('getStatusCode')
                ->will($this->returnValue($status));
        }
        
        return $httpResponse;
    }


    protected function createResponseMock()
    {
        $response = $this->getMock('InoOicClient\Oic\Token\Response');
        return $response;
    }


    protected function createTokenRequestMock()
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\Token\Request')->getMock();
        return $request;
    }


    protected function createResponseHandlerMock($httpResponse = null, $response = null, $error = null)
    {
        $handler = $this->getMock('InoOicClient\Oic\Token\ResponseHandler');
        
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


    public function createErrorMock()
    {
        $error = $this->getMock('InoOicClient\Oic\Error');
        return $error;
    }
}