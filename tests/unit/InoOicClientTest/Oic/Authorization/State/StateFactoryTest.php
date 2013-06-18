<?php

namespace InoOicClientTest\Oic\Authorization\State;

use InoOicClient\Oic\Authorization\State\StateFactory;


class StateFactoryTest extends \PHPUnit_Framework_TestCase
{


    public function testCreateState()
    {
        $factory = new StateFactory();
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\State\State', $factory->createState());
    }
}