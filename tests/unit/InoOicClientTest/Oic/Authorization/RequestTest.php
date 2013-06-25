<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\Request;


class RequestTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $responseType = array(
            'code',
            'token'
        );
        $scope = array(
            'openid',
            'email'
        );
        $state = 'abc';
        $extraParams = array(
            'nonce' => 'bar'
        );
        $clientInfo = $this->createClientInfoMock();
        
        $request = new Request($clientInfo, $responseType, $scope, $state, $extraParams);
        
        $this->assertSame($clientInfo, $request->getClientInfo());
        $this->assertSame($responseType, $request->getResponseType());
        $this->assertSame($scope, $request->getScope());
        $this->assertSame($state, $request->getState());
        
        $params = $request->toArray();
        
        $this->assertArrayHasKey('nonce', $params);
        $this->assertSame('bar', $params['nonce']);
    }


    /**
     * @dataProvider responseTypeProvider
     * @param mixed $requestType
     * @param array $result
     */
    public function testSetResponseType($responseType, $result)
    {
        $request = $this->createRequest();
        $request->setResponseType($responseType);
        $this->assertSame($result, $request->getResponseType());
    }


    /**
     * @dataProvider scopeProvider
     * @param mixed $scope
     * @param array $result
     */
    public function testSetScope($scope, $result)
    {
        $request = $this->createRequest();
        $request->setScope($scope);
        $this->assertSame($result, $request->getScope());
    }


    public function scopeProvider()
    {
        return array(
            array(
                'openid',
                array(
                    'openid'
                )
            ),
            
            array(
                'openid  email',
                array(
                    'openid',
                    'email'
                )
            ),
            
            array(
                'openid  ',
                array(
                    'openid'
                )
            )
        );
    }


    public function responseTypeProvider()
    {
        return array(
            array(
                'code',
                array(
                    'code'
                )
            ),
            
            array(
                'code  token',
                array(
                    'code',
                    'token'
                )
            ),
            
            array(
                'code  ',
                array(
                    'code'
                )
            )
        );
    }


    protected function createRequest()
    {
        $responseType = array(
            'code',
            'token'
        );
        $scope = array(
            'openid',
            'email'
        );
        $state = 'abc';
        $extraParams = array(
            'nonce' => 'bar'
        );
        $clientInfo = $this->createClientInfoMock();
        
        $request = new Request($clientInfo, $responseType, $scope, $state, $extraParams);
        
        return $request;
    }


    protected function createClientInfoMock()
    {
        $clientInfo = $this->getMockBuilder('InoOicClient\Client\ClientInfo')
            ->disableOriginalConstructor()
            ->getMock();
        return $clientInfo;
    }
}