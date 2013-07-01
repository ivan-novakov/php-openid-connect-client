<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\Dispatcher;


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
    
    /*
     * -------
     */
    protected function createResponseFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Oic\Token\ResponseFactoryInterface');
        return $factory;
    }


    protected function createClientAuthenticatorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface');
        return $factory;
    }


    protected function createErrorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Oic\ErrorFactoryInterface');
        return $factory;
    }


    protected function createHttpRequestBuilderMock()
    {
        $builder = $this->getMock('InoOicClient\Oic\Token\HttpRequestBuilder');
        return $builder;
    }
}