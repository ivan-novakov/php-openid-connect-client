<?php

namespace InoOicClientTest\Oic;

use InoOicClient\Oic\AbstractHttpRequestDispatcher;


class AbstractHttpRequestDispatcherTest extends \PHPUnit_Framework_Testcase
{


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
}