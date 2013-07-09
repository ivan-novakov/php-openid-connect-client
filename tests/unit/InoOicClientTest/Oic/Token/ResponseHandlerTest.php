<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\ResponseHandler;


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


    public function testGetResponseFactoryWithImplicitValue()
    {
        $implicitFactory = $this->handler->getResponseFactory();
        $this->assertInstanceOf('InoOicClient\Oic\Token\ResponseFactoryInterface', $implicitFactory);
    }


    public function testSetResponseFactory()
    {
        $responseFactory = $this->createResponseFactoryMock();
        $this->handler->setResponseFactory($responseFactory);
        $this->assertSame($responseFactory, $this->handler->getResponseFactory());
    }


    public function testHandleResponseWithErrorStatusCode()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\HttpErrorStatusException');
        
        $httpResponse = new \Zend\Http\Response();
        $httpResponse->setStatusCode(500);
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithErrorResponse()
    {
        $content = '{"error": "test"}';
        $data = array(
            'error' => 'test'
        );
        $httpResponse = $this->createHttpResponseMock($content, true);
        
        $coder = $this->createJsonCoderMock($content, $data);
        $this->handler->setJsonCoder($coder);
        
        $this->handler->handleResponse($httpResponse);
        $this->assertTrue($this->handler->isError());
        $error = $this->handler->getError();
        $this->assertInstanceOf('InoOicClient\Oic\Error', $error);
        $this->assertSame('test', $error->getCode());
    }


    public function testHandleResponseWithInvalidContent()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\InvalidResponseFormatException');
        
        $content = 'invalid json';
        $httpResponse = $this->createHttpResponseMock($content);
        
        $coder = $this->createJsonCoderMock($content, null, true);
        $this->handler->setJsonCoder($coder);
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithResponseFactoryException()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\InvalidResponseException');
        
        $content = '{"error": "test"}';
        $data = array(
            'foo' => 'bar'
        );
        $httpResponse = $this->createHttpResponseMock($content);
        
        $coder = $this->createJsonCoderMock($content, $data);
        $this->handler->setJsonCoder($coder);
        
        $responseFactory = $this->createResponseFactoryMock($data, null, true);
        $this->handler->setResponseFactory($responseFactory);
        
        $this->handler->handleResponse($httpResponse);
    }


    public function testHandleResponseWithValidResponse()
    {
        $content = '{"error": "test"}';
        $data = array(
            'foo' => 'bar'
        );
        $httpResponse = $this->createHttpResponseMock($content);
        
        $coder = $this->createJsonCoderMock($content, $data);
        $this->handler->setJsonCoder($coder);
        
        $response = $this->getMock('InoOicClient\Oic\Token\Response');
        $responseFactory = $this->createResponseFactoryMock($data, $response);
        $this->handler->setResponseFactory($responseFactory);
        
        $this->handler->handleResponse($httpResponse);
        $this->assertFalse($this->handler->isError());        
        $this->assertSame($response, $this->handler->getResponse());
    }
    
    /*
     * --------------------------------
     */
    protected function createHttpResponseMock($content, $isError = false)
    {
        $httpResponse = $this->getMock('Zend\Http\Response');
        
        $httpResponse->expects($this->once())
            ->method('isSuccess')
            ->will($this->returnValue(! $isError));
        
        if ($content) {
            $httpResponse->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue($content));
        }
        
        return $httpResponse;
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


    protected function createResponseFactoryMock($responseData = null, $response = null, $throwException = false)
    {
        $factory = $this->getMock('InoOicClient\Oic\Token\ResponseFactoryInterface');
        
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
}