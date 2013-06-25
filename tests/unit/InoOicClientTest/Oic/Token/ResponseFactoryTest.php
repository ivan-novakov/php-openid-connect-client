<?php

namespace InoOicClientTest\Oic\Token;

use InoOicClient\Oic\Token\ResponseFactory;


class ResponseFactoryTest extends \PHPUnit_Framework_Testcase
{

/*
    public function testCreateResponseFromHttpResponse()
    {
        $jsonData = '{}';
        $httpResponse = $this->createHttpResponseMock($jsonData);
        $response = $this->createResponseMock();
        
        $factory = $this->getMockBuilder('InoOicClient\Oic\Token\ResponseFactory')
            ->setMethods(array(
            'createResponseFromJson'
        ))
            ->getMock();
        $factory->expects($this->once())
            ->method('createResponseFromJson')
            ->with($jsonData)
            ->will($this->returnValue($response));
        
        $this->assertSame($response, $factory->createResponseFromHttpResponse($httpResponse));
    }


    public function testCreateResponseFromHttpResponseWithEmptyContent()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\InvalidResponseException');
        
        $httpResponse = $this->createHttpResponseMock();
        $factory = new ResponseFactory();
        $factory->createResponseFromHttpResponse($httpResponse);
    }


    public function testCreateResponseFromJson()
    {
        $jsonData = '{}';
        $decodedData = array(
            'foo' => 'bar'
        );
        $response = $this->createResponseMock();
        
        $factory = $this->getMockBuilder('InoOicClient\Oic\Token\ResponseFactory')
            ->setMethods(array(
            'decodeJson',
            'createResponseFromArray'
        ))
            ->getMock();
        
        $factory->expects($this->once())
            ->method('decodeJson')
            ->with($jsonData)
            ->will($this->returnValue($decodedData));
        
        $factory->expects($this->once())
            ->method('createResponseFromArray')
            ->with($decodedData)
            ->will($this->returnValue($response));
        
        $this->assertSame($response, $factory->createResponseFromJson($jsonData));
    }
*/

    public function testCreateResponseFromArray()
    {
        $accessToken = '123';
        $tokenType = 'bearer';
        $data = array(
            'access_token' => $accessToken,
            'token_type' => $tokenType
        );
        
        $factory = new ResponseFactory();
        $response = $factory->createResponse($data);
        
        $this->assertInstanceOf('InoOicClient\Oic\Token\Response', $response);
        $this->assertSame($accessToken, $response->getAccessToken());
        $this->assertSame($tokenType, $response->getTokenType());
    }

/*
    public function testDecodeJson()
    {
        $jsonData = '{"foo": "bar"}';
        $expectedDecodedData = array(
            'foo' => 'bar'
        );
        
        $factory = new ResponseFactory();
        $this->assertSame($expectedDecodedData, $factory->decodeJson($jsonData));
    }


    public function testDecodeJsonWithInvalidInput()
    {
        $this->setExpectedException('InoOicClient\Oic\Token\Exception\InvalidResponseException');
        
        $jsonData = 'invalid json';
        $factory = new ResponseFactory();
        $factory->decodeJson($jsonData);
    }
*/

    protected function createHttpResponseMock($content = null)
    {
        $httpResponse = $this->getMock('Zend\Http\Response');
        $httpResponse->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));
        
        return $httpResponse;
    }


    protected function createResponseMock()
    {
        $response = $this->getMock('InoOicClient\Oic\Token\Response');
        return $response;
    }
}