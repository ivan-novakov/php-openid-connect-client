<?php

namespace InoOicClientTest\Oic;


class AbstractResponseHandlerTest extends \PHPUnit_Framework_TestCase
{

    protected $handler;


    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass('InoOicClient\Oic\AbstractResponseHandler');
    }


    public function testGetJsonCoderWithImplicitValue()
    {
        $jsonCoder = $this->handler->getJsonCoder();
        $this->assertInstanceOf('InoOicClient\Json\Coder', $jsonCoder);
    }


    public function testSetJsonCoder()
    {
        $jsonCoder = $this->createJsonCoderMock();
        $this->handler->setJsonCoder($jsonCoder);
        $this->assertSame($jsonCoder, $this->handler->getJsonCoder());
    }


    public function testGetErrorFactoryWithImplicitValue()
    {
        $errorFactory = $this->handler->getErrorFactory();
        $this->assertInstanceOf('InoOicClient\Oic\ErrorFactoryInterface', $errorFactory);
    }


    public function testSetErrorFactory()
    {
        $errorFactory = $this->createErrorFactoryMock();
        $this->handler->setErrorFactory($errorFactory);
        $this->assertSame($errorFactory, $this->handler->getErrorFactory());
    }


    public function testSetError()
    {
        $error = $this->createErrorMock();
        $this->handler->setError($error);
        $this->assertSame($error, $this->handler->getError());
    }


    public function testIsError()
    {
        $this->assertFalse($this->handler->isError());
        $this->handler->setError($this->createErrorMock());
        $this->assertTrue($this->handler->isError());
    }
    
    /*
     * ----------------------
     */
    protected function createErrorMock()
    {
        $error = $this->getMock('InoOicClient\Oic\Error');
        return $error;
    }


    protected function createJsonCoderMock($input = null, $output = null, $throwsException = false)
    {
        $coder = $this->getMock('InoOicClient\Json\Coder');
        return $coder;
    }


    protected function createErrorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Oic\ErrorFactoryInterface');
        return $factory;
    }
}