<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\Response;


class ResponseTest extends \PHPUnit_Framework_TestCase
{


    public function testSettersAndGetters()
    {
        $code = '123';
        $state = 'abc';
        
        $response = new Response();
        $response->setCode($code);
        $response->setState($state);
        
        $this->assertSame($code, $response->getCode());
        $this->assertSame($state, $response->getState());
    }
}