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
        $builder = $this->dispatcher->getHttpRequestBuilder();
        $this->assertInstanceOf('InoOicClient\Oic\Token\HttpRequestBuilder', $builder);
    }


    public function testSetHttpRequestBuilder()
    {
        $builder = $this->createHttpRequestBuilderMock();
        $this->dispatcher->setHttpRequestBuilder($builder);
        $this->assertSame($builder, $this->dispatcher->getHttpRequestBuilder());
    }


    public function testGetResponseFactoryWithImplicitValue()
    {
        $implicitFactory = $this->dispatcher->getResponseFactory();
        $this->assertInstanceOf('InoOicClient\Oic\Token\ResponseFactoryInterface', $implicitFactory);
    }


    public function testSetResponseFactory()
    {
        $responseFactory = $this->createResponseFactoryMock();
        $this->dispatcher->setResponseFactory($responseFactory);
        $this->assertSame($responseFactory, $this->dispatcher->getResponseFactory());
    }


    public function testSendTokenRequestWithHttpRequestBuilderException()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\HttpRequestBuilderException');
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, null, true);
        $this->dispatcher->setHttpRequestBuilder($builder);
        $this->dispatcher->sendTokenRequest($request);
    }


    public function testSendTokenRequestWithHttpException()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\HttpClientException');
        
        $httpRequest = $this->createHttpRequestMock();
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $this->dispatcher->setHttpRequestBuilder($builder);
        
        $httpClient = $this->createHttpClientMock($httpRequest, null, true);
        $this->dispatcher->setHttpClient($httpClient);
        
        $this->dispatcher->sendTokenRequest($request);
    }


    public function testSendTokenRequestWithInvalidJsonResponse()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\InvalidResponseFormatException');
        
        $jsonString = '{"foo": "bar"}';
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock($jsonString);
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $this->dispatcher->setHttpRequestBuilder($builder);
        
        $httpClient = $this->createHttpClientMock($httpRequest, $httpResponse);
        $this->dispatcher->setHttpClient($httpClient);
        
        $coder = $this->createJsonCoderMock($jsonString, null, true);
        $this->dispatcher->setJsonCoder($coder);
        
        $this->dispatcher->sendTokenRequest($request);
    }


    public function testSendTokenRequestWithErrorHttpStatus()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\HttpErrorResponseException');
        
        $jsonString = '{"foo": "bar"}';
        $responseData = array();
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock($jsonString, 500, true);
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $this->dispatcher->setHttpRequestBuilder($builder);
        
        $httpClient = $this->createHttpClientMock($httpRequest, $httpResponse);
        $this->dispatcher->setHttpClient($httpClient);
        
        $coder = $this->createJsonCoderMock($jsonString, $responseData);
        $this->dispatcher->setJsonCoder($coder);
        
        $this->dispatcher->sendTokenRequest($request);
    }


    public function testSendTokenRequestWithErrorResponse()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\ErrorResponseException');
        
        $jsonString = '{"foo": "bar"}';
        $responseData = array(
            Param::ERROR => 'server_error'
        );
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock($jsonString, null, true);
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $this->dispatcher->setHttpRequestBuilder($builder);
        
        $httpClient = $this->createHttpClientMock($httpRequest, $httpResponse);
        $this->dispatcher->setHttpClient($httpClient);
        
        $coder = $this->createJsonCoderMock($jsonString, $responseData);
        $this->dispatcher->setJsonCoder($coder);
        
        $this->dispatcher->sendTokenRequest($request);
    }


    public function testSendTokenWithInvalidResponse()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\InvalidResponseException');
        
        $jsonString = '{"foo": "bar"}';
        $responseData = array(
            'access_token' => 'abc'
        );
        
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock($jsonString);
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $this->dispatcher->setHttpRequestBuilder($builder);
        
        $httpClient = $this->createHttpClientMock($httpRequest, $httpResponse);
        $this->dispatcher->setHttpClient($httpClient);
        
        $coder = $this->createJsonCoderMock($jsonString, $responseData);
        $this->dispatcher->setJsonCoder($coder);
        
        // $response = $this->createResponseMock();
        $responseFactory = $this->createResponseFactoryMock($responseData, null, true);
        $this->dispatcher->setResponseFactory($responseFactory);
        
        $this->dispatcher->sendTokenRequest($request);
    }
    
    
    public function testSendTokenWithValidResponse()
    {
        $jsonString = '{"foo": "bar"}';
        $responseData = array(
            'access_token' => 'abc'
        );
        
        $httpRequest = $this->createHttpRequestMock();
        $httpResponse = $this->createHttpResponseMock($jsonString);
        
        $request = $this->createTokenRequestMock();
        $builder = $this->createHttpRequestBuilderMock($request, $httpRequest);
        $this->dispatcher->setHttpRequestBuilder($builder);
        
        $httpClient = $this->createHttpClientMock($httpRequest, $httpResponse);
        $this->dispatcher->setHttpClient($httpClient);
        
        $coder = $this->createJsonCoderMock($jsonString, $responseData);
        $this->dispatcher->setJsonCoder($coder);
        
        $response = $this->createResponseMock();
        $responseFactory = $this->createResponseFactoryMock($responseData, $response);
        $this->dispatcher->setResponseFactory($responseFactory);
        
        $this->assertSame($response, $this->dispatcher->sendTokenRequest($request));
    }
    
    /*
     * -------
     */
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


    protected function createResponseFactoryMock($responseData = null, $response = null, $throwException = false)
    {
        $factory = $this->getMock('InoOicClient\Oic\Token\ResponseFactoryInterface');
        
        if ($throwException) {
            $factory->expects($this->once())
                ->method('createResponse')
                ->with($responseData)
                ->will($this->throwException(new \Exception()));
        }
        
        if ($responseData && $response) {
            $factory->expects($this->once())
                ->method('createResponse')
                ->with($responseData)
                ->will($this->returnValue($response));
        }
        
        return $factory;
    }


    protected function createClientAuthenticatorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface');
        return $factory;
    }


    protected function createTokenRequestMock()
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\Token\Request')->getMock();
        return $request;
    }


    protected function createHttpClientMock($httpRequest, $httpResponse = null, $throwException = false)
    {
        $httpClient = $this->getMock('Zend\Http\Client');
        
        if ($httpResponse) {
            $httpClient->expects($this->once())
                ->method('send')
                ->with($httpRequest)
                ->will($this->returnValue($httpResponse));
        } elseif ($throwException) {
            $httpClient->expects($this->once())
                ->method('send')
                ->with($httpRequest)
                ->will($this->throwException(new \Exception()));
        }
        
        return $httpClient;
    }


    protected function createJsonCoderMock($jsonString, $result = null, $throwException = false)
    {
        $coder = $this->getMock('InoOicClient\Json\Coder');
        
        if ($throwException) {
            $coder->expects($this->once())
                ->method('decode')
                ->with($jsonString)
                ->will($this->throwException(new \Exception()));
        } elseif ($result) {
            $coder->expects($this->once())
                ->method('decode')
                ->with($jsonString)
                ->will($this->returnValue($result));
        }
        
        return $coder;
    }
}