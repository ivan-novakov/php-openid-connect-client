<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Authorization\Exception\ErrorResponseException;
use Zend\Stdlib\Parameters;
use InoOicClient\Oic\Authorization\Dispatcher;


class DispatcherTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $uriGenerator = $this->createUriGeneratorMock();
        $dispatcher = new Dispatcher($uriGenerator);
        
        $this->assertSame($uriGenerator, $dispatcher->getUriGenerator());
    }


    public function testImplicitUriGenerator()
    {
        $dispatcher = new Dispatcher();
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\UriGenerator', $dispatcher->getUriGenerator());
    }


    public function testSetUriGenerator()
    {
        $dispatcher = new Dispatcher();
        $uriGenerator = $this->createUriGeneratorMock();
        $dispatcher->setUriGenerator($uriGenerator);
        $this->assertSame($uriGenerator, $dispatcher->getUriGenerator());
    }


    public function testGetImplicitResponseFactory()
    {
        $dispatcher = new Dispatcher();
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\ResponseFactoryInterface', 
            $dispatcher->getResponseFactory());
    }


    public function testSetResponseFactory()
    {
        $dispatcher = new Dispatcher();
        $responseFactory = $this->createResponseFactoryMock();
        $dispatcher->setResponseFactory($responseFactory);
        $this->assertSame($responseFactory, $dispatcher->getResponseFactory());
    }


    public function testCreateAuthorizationRequestUriWithoutState()
    {
        $uri = 'https://oic.server.org/authorize?foo=bar';
        
        $request = $this->createAuthorizationRequest();
        $uriGenerator = $this->createUriGeneratorMock($request, $uri);
        
        $dispatcher = new Dispatcher($uriGenerator);
        $this->assertSame($uri, $dispatcher->createAuthorizationRequestUri($request));
    }


    public function testCreateAuthorizationRequestUriWithState()
    {
        $uri = 'https://oic.server.org/authorize?foo=bar';
        $hash = 'a0a0a0a0a';
        
        $request = $this->createAuthorizationRequest();
        $request->expects($this->once())
            ->method('setState')
            ->with($hash);
        
        $uriGenerator = $this->createUriGeneratorMock($request, $uri);
        
        $dispatcher = new Dispatcher($uriGenerator);
        
        $state = $this->createStateMock($hash);
        
        $stateManager = $this->createStateManagerMock();
        $stateManager->expects($this->once())
            ->method('initState')
            ->will($this->returnValue($state));
        $dispatcher->setStateManager($stateManager);
        
        $this->assertSame($uri, $dispatcher->createAuthorizationRequestUri($request));
    }


    public function testGetAuthorizationResponseWithError()
    {
        $code = 'error';
        $desc = 'error_desc';
        $uri = 'error_uri';
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock(
            array(
                'error' => $code,
                'error_description' => $desc,
                'error_uri' => $uri
            ));
        
        try {
            $dispatcher->getAuthorizationResponse($httpRequest);
        } catch (ErrorResponseException $e) {
            $error = $e->getError();
            $this->assertSame($code, $error->getCode());
            $this->assertSame($desc, $error->getDescription());
            $this->assertSame($uri, $error->getUri());
            return;
        }
        
        $this->fail('Expected ErrorResponseException was not raised');
    }


    public function testGetAuthorizationResponseWithInvalidState()
    {
        $this->setExpectedException('InoOicClient\Oic\Authorization\Exception\StateException');
        
        $stateHash = 'abc';
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock(array(
            'state' => $stateHash
        ));
        
        $stateManager = $this->createStateManagerMock($stateHash);
        $dispatcher->setStateManager($stateManager);
        
        $dispatcher->getAuthorizationResponse($httpRequest);
    }


    public function testGetAuthorizationResponseWithMissingStateManager()
    {
        $this->setExpectedException('InoOicClient\Oic\Authorization\Exception\MissingStateManagerException');
        
        $stateHash = 'abc';
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock(array(
            'state' => $stateHash
        ));
        
        $dispatcher->getAuthorizationResponse($httpRequest);
    }


    public function testGetAuthorizationResponseWithMissingCode()
    {
        $this->setExpectedException('InoOicClient\Oic\Authorization\Exception\InvalidResponseException');
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock();
        $dispatcher->getAuthorizationResponse($httpRequest);
    }


    public function testGetAuthorizationResponseOkWithoutState()
    {
        $this->markTestIncomplete();
        $code = '123';
        
        $state = $this->createStateMock();
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock(array(
            'code' => $code
        ));
        
        $responseFactory = $this->createResponseFactoryMock($code, $state);
        $dispatcher->getAuthorizationResponse($httpRequest);
    }
    
    // ---------------------------
    protected function createAuthorizationRequest()
    {
        $request = $this->getMockBuilder('InoOicClient\Oic\Authorization\Request')
            ->setMethods(array(
            'setState'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        
        return $request;
    }


    protected function createUriGeneratorMock($request = null, $uri = null)
    {
        $uriGenerator = $this->getMock('InoOicClient\Oic\Authorization\UriGenerator');
        if ($request && $uri) {
            $uriGenerator->expects(($this->once()))
                ->method('createAuthorizationRequestUri')
                ->with($request)
                ->will($this->returnValue($uri));
        }
        return $uriGenerator;
    }


    protected function createStateManagerMock($validateHash = null)
    {
        $manager = $this->getMock('InoOicClient\Oic\Authorization\State\Manager');
        if ($validateHash) {
            $manager->expects($this->once())
                ->method('validateState')
                ->with($validateHash)
                ->will($this->throwException(new \Exception()));
        }
        return $manager;
    }


    protected function createStateMock($hash = null)
    {
        $state = $this->getMockBuilder('InoOicClient\Oic\Authorization\State\State')
            ->setMethods(array(
            'getHash'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        
        if ($hash) {
            $state->expects($this->any())
                ->method('getHash')
                ->will($this->returnValue($hash));
        }
        
        return $state;
    }


    protected function createResponseFactoryMock($code = null, $response = null, $state = null)
    {
        $responseFactory = $this->getMock('InoOicClient\Oic\Authorization\ResponseFactoryInterface');
        if ($code && $response) {
            $responseFactory->expects($this->once())
                ->method('createResponse')
                ->with($code, $state)
                ->will($this->returnValue($response));
        }
        return $responseFactory;
    }


    protected function createHttpRequestMock($queryParams = array())
    {
        $httpRequest = $this->getMock('Zend\Http\Request');
        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue(new Parameters($queryParams)));
        return $httpRequest;
    }
}