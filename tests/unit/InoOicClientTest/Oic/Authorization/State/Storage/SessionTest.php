<?php

namespace InoOicClientTest\Oic\Authorization\State\Storage;

use InoOicClient\Oic\Authorization\State\Storage\Session;


class SessionTest extends \PHPUnit_Framework_TestCase
{


    public function testSaveState()
    {
        $state = $this->createStateMock();
        $container = $this->createSessionContainerMock();
        $container->expects($this->once())
            ->method('offsetSet')
            ->with(Session::VAR_AUTHORIZATION_STATE, $state);
        
        $storage = new Session($container);
        $storage->saveState($state);
    }


    public function testLoadState()
    {
        $state = $this->createStateMock();
        $container = $this->createSessionContainerMock();
        $container->expects($this->once())
            ->method('offsetGet')
            ->with(Session::VAR_AUTHORIZATION_STATE)
            ->will($this->returnValue($state));
        
        $storage = new Session($container);
        $this->assertSame($state, $storage->loadState());
    }


    protected function createSessionContainerMock()
    {
        $container = $this->getMockBuilder('Zend\Session\Container')
            ->disableOriginalConstructor()
            ->getMock();
        
        return $container;
    }


    protected function createStateMock()
    {
        return $this->getMockBuilder('InoOicClient\Oic\Authorization\State\State')
            ->disableOriginalConstructor()
            ->getMock();
    }
}