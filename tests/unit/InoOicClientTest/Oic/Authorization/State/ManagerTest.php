<?php

namespace InoOicClientTest\Oic\Authorization\State;

use InoOicClient\Oic\Authorization\State\Manager;


class ManagerTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $storage = $this->createStorageMock();
        $factory = $this->createFactoryMock();
        $manager = $this->createManager($storage, $factory);
        
        $this->assertSame($storage, $manager->getStorage());
        $this->assertSame($factory, $manager->getFactory());
    }


    public function testConstructorWithImplicitArguments()
    {
        $manager = $this->createManager();
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\State\Storage\StorageInterface', $manager->getStorage());
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\State\StateFactoryInterface', $manager->getFactory());
    }


    public function testInitState()
    {
        $state = $this->createStateMock();
        $factory = $this->createFactoryMock($state);
        $storage = $this->createStorageMock($state);
        
        $manager = $this->createManager($storage, $factory);
        $this->assertSame($state, $manager->initState());
    }


    public function testValidateStateWithNoStoredState()
    {
        $this->setExpectedException('InoOicClient\Oic\Authorization\State\Exception\InvalidLocalStateException');
        
        $stateHash = 'abc';
        
        $storage = $this->createStorageMock();
        $manager = $this->createManager($storage);
        $manager->validateState($stateHash);
    }


    public function testValidateStateWithNoIncomingState()
    {
        $this->setExpectedException('InoOicClient\Oic\Authorization\State\Exception\InvalidRemoteStateException');
        
        $stateHash = 'abc';
        
        $state = $this->createStateMock($stateHash);
        $storage = $this->createStorageMock(null, $state);
        
        $manager = $this->createManager($storage);
        $manager->validateState(null);
    }


    public function testValidateStateWithStateMismatch()
    {
        $this->setExpectedException('InoOicClient\Oic\Authorization\State\Exception\StateMismatchException');
        
        $stateHash = 'abc';
        
        $state = $this->createStateMock($stateHash);
        $storage = $this->createStorageMock(null, $state);
        
        $manager = $this->createManager($storage);
        $manager->validateState('def');
    }


    public function testValidateStateOk()
    {
        $stateHash = 'abc';
        
        $state = $this->createStateMock($stateHash);
        $storage = $this->createStorageMock(null, $state);
        
        $manager = $this->createManager($storage);
        $manager->validateState($stateHash);
    }


    protected function createManager($storage = null, $factory = null)
    {
        return new Manager($storage, $factory);
    }


    protected function createStorageMock($saveState = null, $loadState = null)
    {
        $storage = $this->getMock('InoOicClient\Oic\Authorization\State\Storage\StorageInterface');
        if ($saveState) {
            $storage->expects($this->once())
                ->method('saveState')
                ->with($saveState);
        }
        if ($loadState) {
            $storage->expects($this->once())
                ->method('loadState')
                ->will($this->returnValue($loadState));
        }
        return $storage;
    }


    protected function createFactoryMock($state = null)
    {
        $factory = $this->getMock('InoOicClient\Oic\Authorization\State\StateFactoryInterface');
        if ($state) {
            $factory->expects($this->once())
                ->method('createState')
                ->will($this->returnValue($state));
        }
        return $factory;
    }


    public function createStateMock($hash = null)
    {
        $state = $this->getMockBuilder('InoOicClient\Oic\Authorization\State\State')
            ->setMethods(array(
            'getHash'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        if ($hash) {
            $state->expects($this->once())
                ->method('getHash')
                ->will($this->returnValue($hash));
        }
        return $state;
    }
}