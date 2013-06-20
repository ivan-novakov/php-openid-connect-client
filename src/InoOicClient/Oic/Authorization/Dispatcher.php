<?php

namespace InoOicClient\Oic\Authorization;

use Zend\Http;
use InoOicClient\Oic\Authorization\State;


/**
 * Handles authorization requests and responses.
 */
class Dispatcher
{

    /**
     * Authorization request URI generator.
     * @var UriGenerator
     */
    protected $uriGenerator;

    /**
     * The state storage.
     * @var State\Manager
     */
    protected $stateManager;


    /**
     * Constructor.
     * 
     * @param UriGenerator $uriGenerator
     */
    public function __construct(UriGenerator $uriGenerator = null)
    {
        if (null === $uriGenerator) {
            $uriGenerator = new UriGenerator();
        }
        $this->uriGenerator = $uriGenerator;
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
     * Returns the state factory.
     * 
     * @return StateFactoryInterface
     */
    public function getStateFactory()
    {
        return $this->stateFactory;
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
        
        return $this->uriGenerator->createAuthorizationRequestUri($request);
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
        
        $params = $httpRequest->getQuery();
        
        if ($errorCode = $params->get(Param::ERROR)) {
            $error = $this->createError($errorCode, $params->get(Param::ERROR_DESCRIPTION), 
                $params->get(Param::ERROR_URI));
            
            throw new Exception\ErrorResponseException($error);
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
            throw new Exception\StateException('State manager not initialized, cannot validate incoming state value');
        }
        
        $code = $params->get(Param::CODE);
        if (null === $code) {
            throw new Exception\InvalidResponseException('No code in response');
        }
        
        // FIXME - use factory
        $response = new Response();
        $response->setCode($code);
        $response->setState($stateHash);
        
        return $response;
    }


    protected function createError($code, $description = null, $uri = null)
    {
        $error = new Error();
        $error->setCode($code);
        $error->setDescription($description);
        $error->setUri($uri);
        
        return $error;
    }
}