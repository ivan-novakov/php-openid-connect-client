<?php

namespace InoOicClientTest\Client\Authenticator;

use InoOicClient\Client\Authenticator\AuthenticatorFactory;
use InoOicClient\Client\AuthenticationInfo;


class AuthenticatorFactoryTest extends \PHPUnit_Framework_Testcase
{


    public function testCreateAuthenticatorWithNoAuthenticationInfo()
    {
        $this->setExpectedException('InoOicClient\Client\Authenticator\Exception\MissingAuthenticationInfoException');
        
        $client = $this->getMock('InoOicClient\Client\ClientInfo');
        $factory = new AuthenticatorFactory();
        
        $authenticator = $factory->createAuthenticator($client);
    }


    /**
     * @dataProvider authenticatorProvider
     * 
     * @param string $method
     * @param array $params
     * @param string $instance
     * @param string $exception
     */
    public function testCreateAuthenticatorBasic($method, $params, $instance = null, $exception = null)
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }
        
        $clientId = '123';
        
        $info = $this->getMockBuilder('InoOicClient\Client\AuthenticationInfo')
            ->setMethods(array(
            'getMethod',
            'getParams'
        ))
            ->getMock();
        $info->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));
        $info->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue($params));
        
        $client = $this->getMockBuilder('InoOicClient\Client\ClientInfo')
            ->setMethods(array(
            'getAuthenticationInfo',
            'getClientId'
        ))
            ->getMock();
        $client->expects($this->once())
            ->method('getAuthenticationInfo')
            ->will($this->returnValue($info));
        $client->expects($this->once())
            ->method('getClientId')
            ->will($this->returnValue($clientId));
        
        $factory = new AuthenticatorFactory();
        
        $authenticator = $factory->createAuthenticator($client);
        $this->assertInstanceOf($instance, $authenticator);
    }


    public function authenticatorProvider()
    {
        return array(
            array(
                AuthenticationInfo::METHOD_SECRET_BASIC,
                array(
                    'foo' => 'bar'
                ),
                'InoOicClient\Client\Authenticator\SecretBasic'
            ),
            
            array(
                AuthenticationInfo::METHOD_SECRET_POST,
                array(
                    'foo' => 'bar'
                ),
                'InoOicClient\Client\Authenticator\SecretPost'
            ),
            
            array(
                'nonexistent_method',
                null,
                null,
                'InoOicClient\Client\Authenticator\Exception\UnsupportedAuthenticationMethodException'
            )
        );
    }
}