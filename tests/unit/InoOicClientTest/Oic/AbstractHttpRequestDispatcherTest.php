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
        $options = array(
            'foo' => 'bar'
        );
        
        $dispatcher = $this->getMockBuilder('InoOicClient\Oic\AbstractHttpRequestDispatcher')
            ->setConstructorArgs(array(
            $httpClient,
            $options
        ))
            ->getMockForAbstractClass();
        
        $this->assertSame($httpClient, $dispatcher->getHttpClient());
        $this->assertSame($options, $dispatcher->getOptions()
            ->toArray());
    }


    public function testSetOptions()
    {
        $options = array(
            'foo' => 'bar'
        );
        $this->dispatcher->setOptions($options);
        $this->assertSame($options, $this->dispatcher->getOptions()
            ->toArray());
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


    public function testSendHttpRequestWithException()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\HttpClientException');
        
        $httpRequest = $this->getMock('Zend\Http\Request');
        $httpClient = $this->getMock('Zend\Http\Client');
        $httpClient->expects($this->once())
            ->method('send')
            ->with($httpRequest)
            ->will($this->throwException(new \Exception()));
        
        $this->dispatcher->setHttpClient($httpClient);
        $this->dispatcher->sendHttpRequest($httpRequest);
        
        $this->assertSame($httpRequest, $this->dispatcher->getLastHttpRequest());
    }


    public function testSendHttpRequest()
    {
        $httpRequest = $this->getMock('Zend\Http\Request');
        $httpResponse = $this->getMock('Zend\Http\Response');
        $httpClient = $this->getMock('Zend\Http\Client');
        $httpClient->expects($this->once())
            ->method('send')
            ->with($httpRequest)
            ->will($this->returnValue($httpResponse));
        
        $this->dispatcher->setHttpClient($httpClient);
        $this->assertSame($httpResponse, $this->dispatcher->sendHttpRequest($httpRequest));
        $this->assertSame($httpRequest, $this->dispatcher->getLastHttpRequest());
        $this->assertSame($httpResponse, $this->dispatcher->getLastHttpResponse());
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