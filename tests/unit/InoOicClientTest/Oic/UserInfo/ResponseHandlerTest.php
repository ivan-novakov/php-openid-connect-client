<?php

namespace InoOicClientTest\Oic\UserInfo;

use InoOicClient\Oic\UserInfo\ResponseHandler;


class ResponseHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ResponseHandler
     */
    protected $handler;


    public function setUp()
    {
        $this->handler = new ResponseHandler();
    }


    public function testParseAuthenticationHeaderParamsWithEmptyString()
    {
        $paramsString = '';
        $this->assertSame(array(), $this->handler->parseAuthenticationHeaderParams($paramsString));
    }


    public function testParseAuthenticationHeaderParams()
    {
        $paramsString = 'key1=value1, key2="value2", key3=,key4 ,key5="foo bar"';
        $expected = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key5' => 'foo bar'
        );
        
        $this->assertSame($expected, $this->handler->parseAuthenticationHeaderParams($paramsString));
    }


    public function testParseAuthenticateHeaderValueWithInvalidScheme()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\InvalidResponseFormatException');
        
        $rawValue = 'foo bar';
        $this->handler->parseAuthenticateHeaderValue($rawValue);
    }


    public function testParseAuthenticateHeaderValue()
    {
        $rawValue = 'Bearer key1=value1, key2=value2';
        $expected = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );
        
        $this->assertSame($expected, $this->handler->parseAuthenticateHeaderValue($rawValue));
    }


    public function testGetResponseFactoryWithImplicitValue()
    {
        $implicitFactory = $this->handler->getResponseFactory();
        $this->assertInstanceOf('InoOicClient\Oic\UserInfo\ResponseFactoryInterface', $implicitFactory);
    }


    public function testSetResponseFactory()
    {
        $responseFactory = $this->createResponseFactoryMock();
        $this->handler->setResponseFactory($responseFactory);
        $this->assertSame($responseFactory, $this->handler->getResponseFactory());
    }


    public function testHandleResponseWithGenericHttpError()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\HttpErrorStatusException');
        
        $httpResponse = new \Zend\Http\Response();
        $httpResponse->setStatusCode(500);
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithAuthErrorInvalidScheme()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\InvalidResponseFormatException');
        
        $httpResponse = new \Zend\Http\Response();
        $httpResponse->setStatusCode(401);
        $httpResponse->getHeaders()->addHeaders(array(
            'WWW-Authenticate' => 'foo'
        ));
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithAuthErrorWithoutInfo()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\HttpAuthenticateException');
        
        $httpResponse = new \Zend\Http\Response();
        $httpResponse->setStatusCode(401);
        $httpResponse->getHeaders()->addHeaders(array(
            'WWW-Authenticate' => 'Bearer foo=bar'
        ));
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithServerError()
    {
        $httpResponse = new \Zend\Http\Response();
        $httpResponse->setStatusCode(401);
        $httpResponse->getHeaders()->addHeaders(
            array(
                'WWW-Authenticate' => 'Bearer error="server_error",foo="bar"'
            ));
        
        $this->handler->handleResponse($httpResponse);
        $this->assertTrue($this->handler->isError());
        $error = $this->handler->getError();
        $this->assertInstanceOf('InoOicClient\Oic\Error', $error);
        $this->assertSame('server_error', $error->getCode());
    }


    public function testHandleResponseWithInvalidJson()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\InvalidResponseFormatException');
        
        $content = 'invalid json';
        $httpResponse = $this->createHttpResponseMock($content);
        $coder = $this->createJsonCoderMock($content, null, true);
        $this->handler->setJsonCoder($coder);
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithResponseFactoryError()
    {
        $this->setExpectedException('InoOicClient\Oic\UserInfo\Exception\InvalidResponseException');
        
        $content = '{"foo": "bar"}';
        $values = array(
            'foo' => 'bar'
        );
        $httpResponse = $this->createHttpResponseMock($content);
        $coder = $this->createJsonCoderMock($content, $values);
        $this->handler->setJsonCoder($coder);
        
        $responseFactory = $this->createResponseFactoryMock($values, null, true);
        
        $this->handler->setResponseFactory($responseFactory);
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithValidResponse()
    {
        $content = '{"foo": "bar"}';
        $values = array(
            'foo' => 'bar'
        );
        $response = $this->getMock('InoOicClient\Oic\UserInfo\Response');
        
        $httpResponse = $this->createHttpResponseMock($content);
        $coder = $this->createJsonCoderMock($content, $values);
        $this->handler->setJsonCoder($coder);
        
        $responseFactory = $this->createResponseFactoryMock($values, $response);
        $this->handler->setResponseFactory($responseFactory);
        
        $this->handler->handleResponse($httpResponse);
        $this->assertFalse($this->handler->isError());
        $this->assertSame($response, $this->handler->getResponse());
    }
    
    /*
     * -----------------------------
     */
    protected function createHttpResponseMock($content)
    {
        $httpResponse = $this->getMock('Zend\Http\Response');
        
        if ($content) {
            $httpResponse->expects($this->once())
                ->method('isSuccess')
                ->will($this->returnValue(true));
            $httpResponse->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue($content));
        }
        
        return $httpResponse;
    }


    protected function createResponseFactoryMock($responseData = null, $response = null, $throwException = false)
    {
        $factory = $this->getMock('InoOicClient\Oic\UserInfo\ResponseFactoryInterface');
        
        if ($throwException) {
            $factory->expects($this->once())
                ->method('createResponse')
                ->with($responseData)
                ->will($this->throwException(new \Exception()));
        } elseif ($response) {
            $factory->expects($this->once())
                ->method('createResponse')
                ->with($responseData)
                ->will($this->returnValue($response));
        }
        
        return $factory;
    }


    protected function createJsonCoderMock($input = null, $output = null, $throwsException = false)
    {
        $coder = $this->getMock('InoOicClient\Json\Coder');
        
        if ($throwsException) {
            $coder->expects($this->once())
                ->method('decode')
                ->with($input)
                ->will($this->throwException(new \Exception()));
        } elseif ($output) {
            $coder->expects($this->once())
                ->method('decode')
                ->with($input)
                ->will($this->returnValue($output));
        }
        
        return $coder;
    }


    protected function createErrorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Oic\ErrorFactoryInterface');
        return $factory;
    }
}