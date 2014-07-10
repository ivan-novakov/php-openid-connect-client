<?php

namespace InoOicClientTest\Client;

use InoOicClient\Client\AuthenticationInfo;


class AuthenticationInfoTest extends \PHPUnit_Framework_TestCase
{


    public function testGettersAndSetters()
    {
        $method = 'basic';
        $params = array(
            'foo' => 'bar'
        );
        
        $info = new AuthenticationInfo();
        $info->setMethod($method);
        $info->setParams($params);
        
        $this->assertSame($method, $info->getMethod());
        $this->assertSame($params, $info->getParams());
    }
}