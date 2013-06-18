<?php

namespace InoOicClientTest\Oic\Authorization\State;

use InoOicClient\Oic\Authorization\State\State;


class StateText extends \PHPUnit_Framework_TestCase
{

    protected $state;

    protected $uri = 'http://test.uri/';

    protected $hash = '123';

    protected $ctime = '456';


    public function setUp()
    {
        $this->state = new State($this->hash, $this->uri, $this->ctime);
    }


    public function testConstructor()
    {
        $this->assertSame($this->hash, $this->state->getHash());
        $this->assertSame($this->uri, $this->state->getRequestUri());
        $this->assertSame($this->ctime, $this->state->getCtime());
    }
}