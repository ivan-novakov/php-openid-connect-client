<?php

namespace InoOicClientTest\Oic;


class AbstractHttpRequestDispatcherTest extends \PHPUnit_Framework_Testcase
{

    protected $dispatcher;


    public function setUp()
    {
        $httpClient = $this->getMock('Zend\Http\Client');
        $this->dispatcher = $this->getMockBuilder('InoOicClient\Oic\AbstractHttpRequestDispatcher')
            ->setConstructorArgs(array(
            $httpClient
        ))
            ->getMockForAbstractClass();
    }


    public function testConstructor()
    {
        $httpClient = $this->getMock('Zend\Http\Client');
        
        $dispatcher = $this->getMockBuilder('InoOicClient\Oic\AbstractHttpRequestDispatcher')
            ->setConstructorArgs(array(
            $httpClient
        ))
            ->getMockForAbstractClass();
        
        $this->assertSame($httpClient, $dispatcher->getHttpClient());
    }


    public function testSetHttpClient()
    {
        $httpClientOne = $this->getMock('Zend\Http\Client');
        $httpClientTwo = $this->getMock('Zend\Http\Client');
        
        $dispatcher = $this->getMockBuilder('InoOicClient\Oic\AbstractHttpRequestDispatcher')
            ->setConstructorArgs(array(
            $httpClientOne
        ))
            ->getMockForAbstractClass();
        
        $this->assertSame($httpClientOne, $dispatcher->getHttpClient());
        
        $dispatcher->setHttpClient($httpClientTwo);
        $this->assertSame($httpClientTwo, $dispatcher->getHttpClient());
    }


    public function testGetErrorFactoryWithImplicitValue()
    {
        $errorFactory = $this->dispatcher->getErrorFactory();
        $this->assertInstanceOf('InoOicClient\Oic\ErrorFactoryInterface', $errorFactory);
    }


    public function testSetErrorFactory()
    {
        $errorFactory = $this->createErrorFactoryMock();
        $this->dispatcher->setErrorFactory($errorFactory);
        $this->assertSame($errorFactory, $this->dispatcher->getErrorFactory());
    }


    public function testGetJsonCoderWithImplicitValue()
    {
        $jsonCoder = $this->dispatcher->getJsonCoder();
        $this->assertInstanceOf('InoOicClient\Json\Coder', $jsonCoder);
    }


    public function testSetJsonCoder()
    {
        $jsonCoder = $this->createJsonCoderMock();
        $this->dispatcher->setJsonCoder($jsonCoder);
        $this->assertSame($jsonCoder, $this->dispatcher->getJsonCoder());
    }
    
    /*
     * ----------------------------
     */
    protected function createErrorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Oic\ErrorFactoryInterface');
        return $factory;
    }


    protected function createJsonCoderMock()
    {
        $coder = $this->getMock('InoOicClient\Json\Coder');
        return $coder;
    }
}