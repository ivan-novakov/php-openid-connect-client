<?php

namespace InoOicClientTest\Oic;


class AbstractHttpRequestBuilderTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructor()
    {
        $options = array(
            'foo' => 'bar'
        );
        
        $builder = $this->getMockBuilder('InoOicClient\Oic\AbstractHttpRequestBuilder')
            ->setConstructorArgs(array(
            $options
        ))
            ->getMockForAbstractClass();
        
        $this->assertSame($options, $builder->getOptions()
            ->toArray());
    }


    public function testSetOptions()
    {
        $options = array(
            'foo' => 'bar'
        );
        
        $builder = $this->getMockBuilder('InoOicClient\Oic\AbstractHttpRequestBuilder')->getMockForAbstractClass();
        $builder->setOptions($options);
        $this->assertSame($options, $builder->getOptions()
            ->toArray());
    }
}