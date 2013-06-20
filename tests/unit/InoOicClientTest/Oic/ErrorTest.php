<?php

namespace InoOicClientTest\Oic;

use InoOicClient\Oic\Error;


class ErrorTest extends \PHPUnit_Framework_TestCase
{


    public function testGettersAndSetters()
    {
        $code = 'error';
        $description = 'error description';
        $uri = 'http://example.org/error';
        
        $errorResponse = new Error();
        $errorResponse->setCode($code);
        $errorResponse->setDescription($description);
        $errorResponse->setUri($uri);
        
        $this->assertSame($code, $errorResponse->getCode());
        $this->assertSame($description, $errorResponse->getDescription());
        $this->assertSame($uri, $errorResponse->getUri());
    }
}