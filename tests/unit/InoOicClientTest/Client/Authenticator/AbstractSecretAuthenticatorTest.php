<?php

namespace InoOicClientTest\Client\Authenticator;


class AbstractSecretAuthenticatorTest extends \PHPUnit_Framework_Testcase
{


    public function testConfigureHttpRequest()
    {
        $httpRequest = $this->getMock('Zend\Http\Request');
        $clientId = '123';
        $clientSecret = 'abc';
        
        $authenticator = $this->getMockBuilder('InoOicClient\Client\Authenticator\AbstractSecretAuthenticator')
            ->setConstructorArgs(
            array(
                $clientId,
                array(
                    'client_secret' => $clientSecret
                )
            ))
            ->setMethods(array(
            'setAuth'
        ))
            ->getMockForAbstractClass();
        
        $authenticator->expects($this->once())
            ->method('setAuth')
            ->with($httpRequest, $clientId, $clientSecret);
        
        $authenticator->configureHttpRequest($httpRequest);
    }
}