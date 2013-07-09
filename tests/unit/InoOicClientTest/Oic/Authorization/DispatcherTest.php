<?php

namespace InoOicClientTest\Oic\Authorization;

use InoOicClient\Oic\Exception\ErrorResponseException;
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
        $this->assertInstanceOf('InoOicClient\Oic\Authorization\Uri\Generator', $dispatcher->getUriGenerator());
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
        $this->assertSame($request, $dispatcher->getLastRequest());
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
        $this->assertSame($request, $dispatcher->getLastRequest());
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
            $this->assertSame($httpRequest, $dispatcher->getLastHttpRequestFromServer());
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
        
        $stateManager = $this->createStateManagerMock($stateHash, true);
        $dispatcher->setStateManager($stateManager);
        
        $dispatcher->getAuthorizationResponse($httpRequest);
        $this->assertSame($httpRequest, $dispatcher->getLastHttpRequestFromServer());
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
        $this->assertSame($httpRequest, $dispatcher->getLastHttpRequestFromServer());
    }


    public function testGetAuthorizationResponseWithMissingCode()
    {
        $this->setExpectedException('InoOicClient\Oic\Authorization\Exception\InvalidResponseException');
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock();
        $dispatcher->getAuthorizationResponse($httpRequest);
        $this->assertSame($httpRequest, $dispatcher->getLastHttpRequestFromServer());
    }


    public function testGetAuthorizationResponseOkWithoutState()
    {
        $code = '123';
        
        $state = $this->createStateMock();
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock(array(
            'code' => $code
        ));
        
        $response = $this->createResponseMock();
        
        $responseFactory = $this->createResponseFactoryMock($code, $response);
        $dispatcher->setResponseFactory($responseFactory);
        
        $this->assertSame($response, $dispatcher->getAuthorizationResponse($httpRequest));
        $this->assertSame($httpRequest, $dispatcher->getLastHttpRequestFromServer());
        $this->assertSame($response, $dispatcher->getLastResponse());
    }


    public function testGetAuthorizationResponseOkWithState()
    {
        $code = '123';
        $stateHash = 'abc';
        
        $state = $this->createStateMock();
        
        $dispatcher = new Dispatcher();
        $httpRequest = $this->createHttpRequestMock(
            array(
                'code' => $code,
                'state' => $stateHash
            ));
        
        $response = $this->createResponseMock();
        
        $responseFactory = $this->createResponseFactoryMock($code, $response, $stateHash);
        $dispatcher->setResponseFactory($responseFactory);
        
        $stateManager = $this->createStateManagerMock($stateHash);
        $dispatcher->setStateManager($stateManager);
        
        $this->assertSame($response, $dispatcher->getAuthorizationResponse($httpRequest));
        $this->assertSame($httpRequest, $dispatcher->getLastHttpRequestFromServer());
        $this->assertSame($response, $dispatcher->getLastResponse());
    }
    
    /*
     * -----------------------------------------------------------------------------
     */
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
        $uriGenerator = $this->getMock('InoOicClient\Oic\Authorization\Uri\Generator');
        if ($request && $uri) {
            $uriGenerator->expects(($this->once()))
                ->method('createAuthorizationRequestUri')
                ->with($request)
                ->will($this->returnValue($uri));
        }
        return $uriGenerator;
    }


    protected function createStateManagerMock($validateHash = null, $throwException = false)
    {
        $manager = $this->getMock('InoOicClient\Oic\Authorization\State\Manager');
        if ($validateHash) {
            if ($throwException) {
                $manager->expects($this->once())
                    ->method('validateState')
                    ->with($validateHash)
                    ->will($this->throwException(new \Exception()));
            } else {
                $manager->expects($this->once())
                    ->method('validateState')
                    ->with($validateHash);
            }
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


    protected function createResponseMock()
    {
        $response = $this->getMock('InoOicClient\Oic\Authorization\Response');
        return $response;
    }
}