<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\Dispatcher;


class DispatcherTest extends \PHPUnit_Framework_TestCase
{


    public function testCreateAuthorizationRequestUriWithoutState()
    {
        $uri = 'https://oic.server.org/authorize?foo=bar';
        
        $request = $this->createAuthorizationRequest();
        
        $uriGenerator = $this->createUriGeneratorMock($request, $uri);
        
        $dispatcher = new Dispatcher($uriGenerator);
        
        $this->assertSame($uri, $dispatcher->createAuthorizationRequestUri($request));
    }


    public function testCreateAuthorizationRequestUriWithState()
    {
        $uri = 'https://oic.server.org/authorize?foo=bar';
        $hash = 'a0a0a0a0a';
        
        $request = $this->createAuthorizationRequest();
        $request->expects($this->once())
            ->method('setState')
            ->with($hash);
        
        $uriGenerator = $this->createUriGeneratorMock($request, $uri);
        
        $dispatcher = new Dispatcher($uriGenerator);
        
        $state = $this->createStateMock($hash);
        $stateFactory = $this->createStateFactoryMock();
        $stateFactory->expects($this->once())
            ->method('createState')
            ->will($this->returnValue($state));
        $dispatcher->setStateFactory($stateFactory);
        
        $stateStorage = $this->createStateStorageMock();
        $stateStorage->expects($this->once())
            ->method('saveState')
            ->with($state);
        $dispatcher->setStateStorage($stateStorage);
        
        $this->assertSame($uri, $dispatcher->createAuthorizationRequestUri($request));
    }


    protected function createAuthorizationRequest()
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\Authorization\Request')
            ->setMethods(array(
            'setState'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        
        return $request;
    }


    protected function createUriGeneratorMock($request, $uri)
    {
        $uriGenerator = $this->getMock('InoOicClient\Oic\Authorization\UriGenerator');
        $uriGenerator->expects(($this->once()))
            ->method('createAuthorizationRequestUri')
            ->with($request)
            ->will($this->returnValue($uri));
        
        return $uriGenerator;
    }


    protected function createStateFactoryMock()
    {
        $factory = $this->getMock('InoOicClient\Oic\Authorization\State\StateFactoryInterface');
        
        return $factory;
    }


    protected function createStateStorageMock()
    {
        $storage = $this->getMock('InoOicClient\Oic\Authorization\State\Storage\StorageInterface');
        
        return $storage;
    }


    protected function createStateMock($hash)
    {
        $state = $this->getMockBuilder('InoOicClient\Oic\Authorization\State\State')
            ->setMethods(array(
            'getHash'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        $state->expects($this->any())
            ->method('getHash')
            ->will($this->returnValue($hash));
        
        return $state;
    }
}