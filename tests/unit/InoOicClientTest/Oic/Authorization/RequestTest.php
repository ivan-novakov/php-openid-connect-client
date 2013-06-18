<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\Request;


class RequestTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $endpointUri = 'https://oic.server.org/authorize';
        $responseType = array(
            'code',
            'token'
        );
        $clientId = '123';
        $redirectUri = 'https://redirect';
        $scope = array(
            'openid',
            'email'
        );
        $state = 'abc';
        $extraParams = array(
            'foo' => 'bar'
        );
        
        $request = new Request($endpointUri, $responseType, $clientId, $redirectUri, $scope, $state, $extraParams);
        
        $this->assertSame($endpointUri, $request->getEndpointUri());
        $this->assertSame($responseType, $request->getResponseType());
        $this->assertSame($clientId, $request->getClientId());
        $this->assertSame($redirectUri, $request->getRedirectUri());
        $this->assertSame($scope, $request->getScope());
        $this->assertSame($state, $request->getState());
        
        $params = $request->toArray();
        
        $this->assertArrayHasKey('foo', $params);
        $this->assertSame('bar', $params['foo']);
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
        $clientId = '123';
        $redirectUri = 'https://redirect';
        $scope = array(
            'openid',
            'email'
        );
        $state = 'abc';
        $extraParams = array(
            'foo' => 'bar'
        );
        
        $request = new Request($responseType, $clientId, $redirectUri, $scope, $state, $extraParams);
        
        return $request;
    }
}