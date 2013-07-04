<?php

namespace InoOicClientTest\Http;

use InoOicClient\Http\ClientFactory;


class ClientFactoryTest extends \PHPUnit_Framework_Testcase
{

    protected $factory;


    public function setUp()
    {
        $this->factory = new ClientFactory();
    }


    public function testSetDefaultOptions()
    {
        $options = array(
            'foo' => 'bar'
        );
        $this->factory->setDefaultOptions($options);
        $this->assertSame($options, $this->factory->getDefaultOptions());
    }


    public function testMergeOptions()
    {
        $defaultOptions = array(
            'foo' => 'bar',
            'replace' => 'value',
            'sub' => array(
                'subfoo' => 'subbar',
                'subreplace' => 'subvalue'
            )
        );
        
        $options = array(
            'new' => 'option',
            'replace' => 'replacedvalue',
            'sub' => array(
                'subnew' => 'suboption',
                'subreplace' => 'subreplacedvalue'
            )
        );
        
        $merged = array(
            'foo' => 'bar',
            'replace' => 'replacedvalue',
            'new' => 'option',
            'sub' => array(
                'subfoo' => 'subbar',
                'subreplace' => 'subreplacedvalue',
                'subnew' => 'suboption'
            )
        );
        
        $this->factory->setDefaultOptions($defaultOptions);
        $this->assertEquals($merged, $this->factory->mergeOptions($options));
    }


    public function testCreateHttpClient()
    {
        $options = array(
            'foo' => 'bar'
        );
        $mergedOptions = array(
            'foo2' => 'bar2'
        );
        
        $factory = $this->getMockBuilder('InoOicClient\Http\ClientFactory')
            ->setMethods(array(
            'mergeOptions'
        ))
            ->getMock();
        
        $factory->expects($this->once())
            ->method('mergeOptions')
            ->with($options)
            ->will($this->returnValue($mergedOptions));
        
        $httpClient = $factory->createHttpClient($options);
        $this->assertInstanceOf('Zend\Http\Client', $httpClient);
    }
}