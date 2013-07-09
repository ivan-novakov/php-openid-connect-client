<?php

namespace InoOicClient\Oic\Authorization;

use InoOicClient\Oic\Exception\ErrorResponseException;
use InoOicClient\Oic\Error;
use InoOicClient\Oic\Authorization\State;
use InoOicClient\Oic\ErrorFactoryInterface;
use InoOicClient\Oic\ErrorFactory;
use Zend\Http;


/**
 * Handles authorization requests and responses.
 */
class Dispatcher
{

    /**
     * Authorization request URI generator.
     * @var Uri\Generator
     */
    protected $uriGenerator;

    /**
     * The state storage.
     * @var State\Manager
     */
    protected $stateManager;

    /**
     * Response factory.
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var ErrorFactoryInterface
     */
    protected $errorFactory;

    /**
     * @var Request
     */
    protected $lastRequest;

    /**
     * @var Response
     */
    protected $lastResponse;

    /**
     * The last HTTP request from the server, sent to the redirect URI endpoint.
     * @var Http\Request
     */
    protected $lastHttpRequestFromServer;


    /**
     * Constructor.
     * 
     * @param Uri\Generator $uriGenerator
     */
    public function __construct(Uri\Generator $uriGenerator = null)
    {
        if (null !== $uriGenerator) {
            $this->setUriGenerator($uriGenerator);
        }
    }


    /**
     * Sets the URI generator.
     *
     * @param Uri\Generator $uriGenerator
     */
    public function setUriGenerator(Uri\Generator $uriGenerator)
    {
        $this->uriGenerator = $uriGenerator;
    }


    /**
     * Returns the URI generator.
     * 
     * @return Uri\Generator
     */
    public function getUriGenerator()
    {
        if (! $this->uriGenerator instanceof Uri\Generator) {
            $this->uriGenerator = new Uri\Generator();
        }
        return $this->uriGenerator;
    }


    /**
     * Sets the state manager.
     * 
     * @param State\Manager $stateStorage
     */
    public function setStateManager(State\Manager $stateManager)
    {
        $this->stateManager = $stateManager;
    }


    /**
     * Returns the state manager.
     * 
     * @return State\Manager
     */
    public function getStateManager()
    {
        return $this->stateManager;
    }


    /**
     * Returns the response factory.
     * 
     * @return ResponseFactoryInterface
     */
    public function getResponseFactory()
    {
        if (! $this->responseFactory instanceof ResponseFactoryInterface) {
            $this->responseFactory = new ResponseFactory();
        }
        return $this->responseFactory;
    }


    /**
	 * Sets the response factory.
	 * 
     * @param ResponseFactoryInterface $responseFactory
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


    /**
     * @return ErrorFactoryInterface
     */
    public function getErrorFactory()
    {
        if (! $this->errorFactory instanceof ErrorFactoryInterface) {
            $this->errorFactory = new ErrorFactory();
        }
        return $this->errorFactory;
    }


    /**
     * @param ErrorFactoryInterface $errorFactory
     */
    public function setErrorFactory($errorFactory)
    {
        $this->errorFactory = $errorFactory;
    }


    /**
     * Generates a request URI for the corresponding authorization request.
     * 
     * @param Request $request
     * @return string
     */
    public function createAuthorizationRequestUri(Request $request)
    {
        if ($stateManager = $this->getStateManager()) {
            $state = $stateManager->initState();
            $request->setState($state->getHash());
        }
        
        $this->setLastRequest($request);
        return $this->getUriGenerator()->createAuthorizationRequestUri($request);
    }


    /**
     * Validates and returns the authorization response.
     * 
     * @param Http\Request $httpRequest
     * @throws Exception\ErrorResponseException
     * @throws Exception\StateException
     * @throws Exception\InvalidResponseException
     * @return Response
     */
    public function getAuthorizationResponse(Http\Request $httpRequest = null)
    {
        if (null === $httpRequest) {
            $httpRequest = new Http\PhpEnvironment\Request();
        }
        $this->setLastHttpRequestFromServer($httpRequest);
        $params = $httpRequest->getQuery();
        
        if ($errorCode = $params->get(Param::ERROR)) {
            $error = $this->getErrorFactory()->createError($errorCode, $params->get(Param::ERROR_DESCRIPTION), 
                $params->get(Param::ERROR_URI));
            
            throw new ErrorResponseException($error);
        }
        
        $stateHash = $params->get(Param::STATE);
        
        if ($stateManager = $this->getStateManager()) {
            try {
                $stateManager->validateState($stateHash);
            } catch (\Exception $e) {
                throw new Exception\StateException(
                    sprintf("State validation exception: [%s] %s", get_class($e), $e->getMessage()), null, $e);
            }
        } elseif (null !== $stateHash) {
            throw new Exception\MissingStateManagerException(
                'State manager not initialized, cannot validate incoming state value');
        }
        
        $code = $params->get(Param::CODE);
        if (null === $code) {
            throw new Exception\InvalidResponseException('No code in response');
        }
        
        $response = $this->getResponseFactory()->createResponse($code, $stateHash);
        $this->setLastResponse($response);
        return $response;
    }


    /**
     * Returns the last authorization request.
     * 
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }


    /**
     * Returns the last (successful) authorization response.
     * 
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }


    /**
     * Returns the last HTTP request from the server sent to the "redirect URI" endpoint.
     * @return Http\Request
     */
    public function getLastHttpRequestFromServer()
    {
        return $this->lastHttpRequestFromServer;
    }


    /**
     * Sets the last authorization request.
     * 
     * @param Request $request
     */
    protected function setLastRequest(Request $request)
    {
        $this->lastRequest = $request;
    }


    /**
     * Sets the last authorization response.
     * 
     * @param Response $response
     */
    protected function setLastResponse(Response $response)
    {
        $this->lastResponse = $response;
    }


    /**
     * Sets the last HTTP request from the server sent to the "redirect URI" endpoint.
     * 
     * @param Http\Request $httpRequest
     */
    protected function setLastHttpRequestFromServer(Http\Request $httpRequest)
    {
        $this->lastHttpRequestFromServer = $httpRequest;
    }
}