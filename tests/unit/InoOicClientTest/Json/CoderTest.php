<?php

namespace InoOicClientTest\Json;

use InoOicClient\Json\Coder;


class CoderTest extends \PHPUnit_Framework_TestCase
{

    protected $coder;


    public function setUp()
    {
        $this->coder = new Coder();
    }


    public function testDecode()
    {
        $json = '{"foo": "bar"}';
        $result = array(
            'foo' => 'bar'
        );
        
        $this->assertSame($result, $this->coder->decode($json));
    }
    
    
    public function testDecodeWithInvalidJson()
    {
        $this->setExpectedException('InoOicClient\Json\Exception\DecodeException');
        
        $json = 'invalid json';
        $this->coder->decode($json);
    }
}