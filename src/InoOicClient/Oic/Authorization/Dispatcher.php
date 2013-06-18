<?php

namespace InoOicClient\Oic\Authorization;

use InoOicClient\Oic\Authorization\State\Storage\StorageInterface;
use InoOicClient\Oic\Authorization\State\StateFactoryInterface;


class Dispatcher
{

    /**
     * Authorization request URI generator.
     * @var UriGenerator
     */
    protected $uriGenerator;

    /**
     * The state storage.
     * @var StorageInterface
     */
    protected $stateStorage;

    /**
     * The state factory.
     * @var StateFactoryInterface
     */
    protected $stateFactory;


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
     * Sets the state storage.
     * 
     * @param StorageInterface $stateStorage
     */
    public function setStateStorage(StorageInterface $stateStorage)
    {
        $this->stateStorage = $stateStorage;
    }


    /**
     * Returns the state storage.
     * 
     * @return StorageInterface
     */
    public function getStateStorage()
    {
        return $this->stateStorage;
    }


    /**
     * Sets the state factory.
     *
     * @param StateFactoryInterface $stateFactory
     */
    public function setStateFactory($stateFactory)
    {
        $this->stateFactory = $stateFactory;
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
        $stateStorage = $this->getStateStorage();
        $stateFactory = $this->getStateFactory();
        if ($stateStorage && $stateFactory) {
            $state = $this->getStateFactory()->createState();
            $this->stateStorage->saveState($state);
            $request->setState($state->getHash());
        }
        
        return $this->uriGenerator->createAuthorizationRequestUri($request);
    }
}