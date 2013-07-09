<?php

namespace InoOicClientTest\Flow;

use InoOicClient\Flow\Basic;


class AbstractFlowTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructor()
    {
        $options = array(
            'foo' => 'bar'
        );
        $flow = $this->createFlow($options);
        $this->assertSame('bar', $flow->getOptions()
            ->get('foo'));
    }


    public function testSetOptions()
    {
        $flow = $this->createFlow();
        $options = array(
            'foo' => 'bar'
        );
        $flow->setOptions($options);
        $this->assertSame('bar', $flow->getOptions()
            ->get('foo'));
    }


    public function testGetStateManagerWithImplicitValue()
    {
        $flow = $this->createFlow();
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\State\Manager', $flow->getStateManager());
    }


    public function testSetStateManager()
    {
        $flow = $this->createFlow();
        $stateManager = $this->createStateManagerMock();
        $flow->setStateManager($stateManager);
        $this->assertSame($stateManager, $flow->getStateManager());
    }


    public function testGetAuthorizationDispatcherWithImplicitValue()
    {
        $flow = $this->createFlow();
        $stateManager = $this->createStateManagerMock();
        $flow->setStateManager($stateManager);
        $dispatcher = $flow->getAuthorizationDispatcher();
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\Dispatcher', $dispatcher);
        $this->assertSame($stateManager, $dispatcher->getStateManager());
    }


    public function testSetAuthorizationDispatcher()
    {
        $flow = $this->createFlow();
        $dispatcher = $this->getMock('InoOicClient\Oic\Authorization\Dispatcher');
        $flow->setAuthorizationDispatcher($dispatcher);
        $this->assertSame($dispatcher, $flow->getAuthorizationDispatcher());
    }


    public function testGetTokenDispatcherWithImplicitValue()
    {
        $tokenDispatcherOptions = array(
            'foo' => 'bar'
        );
        $options = array(
            'token_dispatcher' => $tokenDispatcherOptions
        );
        
        $flow = $this->createFlow($options);
        $httpClient = $this->createHttpClientMock();
        $flow->setHttpClient($httpClient);
        $dispatcher = $flow->getTokenDispatcher();
        $this->assertInstanceOf('InoOicClient\Oic\Token\Dispatcher', $dispatcher);
        $this->assertSame($httpClient, $dispatcher->getHttpClient());
        $this->assertSame($tokenDispatcherOptions, $dispatcher->getOptions()->toArray());
    }


    public function testSetTokenDispatcher()
    {
        $flow = $this->createFlow();
        $dispatcher = $this->getMock('InoOicClient\Oic\Token\Dispatcher');
        $flow->setTokenDispatcher($dispatcher);
        $this->assertSame($dispatcher, $flow->getTokenDispatcher());
    }


    public function testGetUserInfoDispatcherWithImplicitValue()
    {
        $flow = $this->createFlow();
        $httpClient = $this->createHttpClientMock();
        $flow->setHttpClient($httpClient);
        $dispatcher = $flow->getUserInfoDispatcher();
        $this->assertInstanceOf('InoOicClient\Oic\UserInfo\Dispatcher', $dispatcher);
        $this->assertSame($httpClient, $dispatcher->getHttpClient());
    }


    public function testSetUserInfoDispatcher()
    {
        $flow = $this->createFlow();
        $dispatcher = $this->getMock('InoOicClient\Oic\UserInfo\Dispatcher');
        $flow->setUserInfoDispatcher($dispatcher);
        $this->assertSame($dispatcher, $flow->getUserInfoDispatcher());
    }


    public function testGetClientInfoWithImplicitValue()
    {
        $options = array(
            Basic::OPT_CLIENT_INFO => array(
                'client_id' => '123'
            )
        );
        
        $flow = $this->createFlow($options);
        $clientInfo = $flow->getClientInfo();
        $this->assertInstanceOf('InoOicClient\Client\ClientInfo', $clientInfo);
        $this->assertSame('123', $clientInfo->getClientId());
    }


    public function testSetClientInfo()
    {
        $flow = $this->createFlow();
        $clientInfo = $this->getMock('InoOicClient\Client\ClientInfo');
        $flow->setClientInfo($clientInfo);
        $this->assertSame($clientInfo, $flow->getClientInfo());
    }


    public function testGetHttpClientFactoryWithImplicitValue()
    {
        $flow = $this->createFlow();
        $factory = $flow->getHttpClientFactory();
        $this->assertInstanceOf('InoOicClient\Http\ClientFactory', $factory);
    }


    public function testSetHttpClientFactory()
    {
        $flow = $this->createFlow();
        $factory = $this->getMock('InoOicClient\Http\ClientFactory');
        $flow->setHttpClientFactory($factory);
        $this->assertSame($factory, $flow->getHttpClientFactory());
    }


    public function testGetHttpClientWithImplicitValue()
    {
        $httpOptions = array(
            'foo' => 'bar'
        );
        $options = array(
            Basic::OPT_HTTP_CLIENT => $httpOptions
        );
        $httpClient = $this->createHttpClientMock();
        $factory = $this->getMock('InoOicClient\Http\ClientFactory');
        $factory->expects($this->once())
            ->method('createHttpClient')
            ->with($httpOptions)
            ->will($this->returnValue($httpClient));
        
        $flow = $this->createFlow($options);
        $flow->setHttpClientFactory($factory);
        
        $this->assertSame($httpClient, $flow->getHttpClient());
    }


    public function testSetHttpClient()
    {
        $flow = $this->createFlow();
        $httpClient = $this->createHttpClientMock();
        $flow->setHttpClient($httpClient);
        $this->assertSame($httpClient, $flow->getHttpClient());
    }
    
    /*
     * --------------------
     */
    protected function createFlow($options = array())
    {
        $flow = $this->getMockBuilder('InoOicClient\Flow\AbstractFlow')
            ->setConstructorArgs(array(
            $options
        ))
            ->getMockForAbstractClass();
        return $flow;
    }


    protected function createStateManagerMock()
    {
        $stateManager = $this->getMock('InoOicClient\Oic\Authorization\State\Manager');
        return $stateManager;
    }


    protected function createHttpClientMock()
    {
        $httpClient = $this->getMock('Zend\Http\Client');
        return $httpClient;
    }
}