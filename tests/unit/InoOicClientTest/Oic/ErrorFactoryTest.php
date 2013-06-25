<?php

namespace InoOicClientTest\Oic;

use InoOicClient\Oic\ErrorFactory;


class ErrorFactoryTest extends \PHPUnit_Framework_Testcase
{


    public function testCreateError()
    {
        $code = 'abc';
        $description = 'qwe';
        $uri = 'http://server/error';
        
        $factory = new ErrorFactory();
        $error = $factory->createError($code, $description, $uri);
        
        $this->assertInstanceOf('InoOicClient\Oic\Error', $error);
        $this->assertSame($code, $error->getCode());
        $this->assertSame($description, $error->getDescription());
        $this->assertSame($uri, $error->getUri());
    }
}