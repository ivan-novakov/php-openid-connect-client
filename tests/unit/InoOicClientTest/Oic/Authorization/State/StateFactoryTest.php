<?php

namespace InoOicClientTest\Oic\Authorization\State;

use InoOicClient\Oic\Authorization\State\StateFactory;


class StateFactoryTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructWithImplicitParams()
    {
        $factory = new StateFactory();
        $this->assertSame(32, strlen($factory->getHash()));
    }


    public function testConstructWithSpecificParams()
    {
        $hash = 'bar';
        
        $factory = new StateFactory($hash);
        $this->assertSame($hash, $factory->getHash());
    }


    public function testCreateState()
    {
        $factory = new StateFactory();
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\State\State', $factory->createState());
    }


    public function testCreateStateWithSpecificUri()
    {
        $uri = 'https://some/uri';
        
        $factory = new StateFactory();
        $state = $factory->createState($uri);
        
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\State\State', $state);
        $this->assertSame($uri, $state->getRequestUri());
    }
}