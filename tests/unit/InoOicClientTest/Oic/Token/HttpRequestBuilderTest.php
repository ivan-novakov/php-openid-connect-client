<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\HttpRequestBuilder;


class HttpRequestBuilderTest extends \PHPUnit_Framework_Testcase
{

    protected $builder;


    public function setUp()
    {
        $this->builder = new HttpRequestBuilder();
    }


    public function testGetClientAuthenticatorFactoryWithImplicitValue()
    {
        $factory = $this->builder->getClientAuthenticatorFactory();
        $this->assertInstanceOf('InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface', $factory);
    }


    public function testSetClientAuthenticatorFactory()
    {
        $factory = $this->createClientAuthenticatorFactoryMock();
        $this->builder->setClientAuthenticatorFactory($factory);
        $this->assertSame($factory, $this->builder->getClientAuthenticatorFactory());
    }
    
    /*
     * --------------
     */
    protected function createClientAuthenticatorFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface');
        return $factory;
    }
}