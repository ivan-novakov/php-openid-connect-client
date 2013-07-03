<?php

namespace InoOicClientTest\Oic\UserInfo;

use InoOicClient\Oic\UserInfo\Response;


class ResponseTest extends \PHPUnit_Framework_TestCase
{


    public function testGettersAndSetters()
    {
        $claims = array(
            'foo' => 'bar'
        );
        
        $response = new Response();
        $response->setClaims($claims);
        $this->assertSame($claims, $response->getClaims());
    }
}