<?php

namespace InoOicClientTest\Client\Authenticator;

use InoOicClient\Client\Authenticator\SecretPost;
use InoOicClient\Oic\Token\Param;


class SecretPostTest extends \PHPUnit_Framework_Testcase
{


    public function testSetAuth()
    {
        $clientId = '123';
        $clientSecret = 'abc';
        
        /*
         * Quick fix:
         * PHP Fatal error:  Class Mock_ParametersInterface_b9f9c615 must implement interface Traversable as part 
         * of either Iterator or IteratorAggregate in Unknown on line 0
         */
        //$postParams = $this->getMock('Zend\Stdlib\ParametersInterface');
        $postParams = $this->getMock('Zend\Stdlib\Parameters');
        
        $postParams->expects($this->at(0))
            ->method('set')
            ->with(Param::CLIENT_ID, $clientId);
        $postParams->expects($this->at(1))
            ->method('set')
            ->with(Param::CLIENT_SECRET, $clientSecret);
        
        $httpRequest = $this->getMock('Zend\Http\Request');
        $httpRequest->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($postParams));
        
        $authenticator = new SecretPost($clientId);
        $authenticator->setAuth($httpRequest, $clientId, $clientSecret);
    }
}