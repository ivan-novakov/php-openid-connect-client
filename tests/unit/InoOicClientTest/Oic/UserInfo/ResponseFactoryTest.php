<?php

namespace InoOicClientTest\Oic\UserInfo;

use InoOicClient\Oic\UserInfo\ResponseFactory;


class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{


    public function testCreateResponse()
    {
        $responseData = array(
            'foo' => 'bar'
        );
        
        $factory = new ResponseFactory();
        $response = $factory->createResponse($responseData);
        
        $this->assertInstanceOf('InoOicClient\Oic\UserInfo\Response', $response);
        $this->assertSame($responseData, $response->getClaims());
    }
}