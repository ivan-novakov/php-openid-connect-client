<?php

namespace InoOicClientTest\Oic;

use InoOicClient\Oic\ErrorFactory;
use InoOicClient\Oic\Token\Param;


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


    public function testCreateErrorFromArrayWithNoCode()
    {
        $this->setExpectedException('InoOicClient\Oic\Exception\InvalidErrorCodeException');
        
        $factory = new ErrorFactory();
        $factory->createErrorFromArray(array());
    }


    public function testCreateErrorFromArray()
    {
        $code = 'error';
        $description = 'error_desc';
        $uri = 'http://error/uri';
        
        $data = array(
            Param::ERROR => $code,
            Param::ERROR_DESCRIPTION => $description,
            Param::ERROR_URI => $uri
        );
        
        $factory = new ErrorFactory();
        $error = $factory->createErrorFromArray($data);
        
        $this->assertInstanceOf('InoOicClient\Oic\Error', $error);
        $this->assertSame($code, $error->getCode());
        $this->assertSame($description, $error->getDescription());
        $this->assertSame($uri, $error->getUri());
    }
}