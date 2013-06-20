<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\ResponseFactory;


class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{


    public function testCreateResponse()
    {
        $code = '123';
        $state = 'abc';
        
        $factory = new ResponseFactory();
        $response = $factory->createResponse($code, $state);
        
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\Response', $response);
        $this->assertSame($code, $response->getCode());
        $this->assertSame($state, $response->getState());
    }
}