<?php

namespace InoOicClient\Oic\Authorization\State;


class Manager
{

    /**
     * State storage.
     * @var Storage\StorageInterface
     */
    protected $storage;

    /**
     * State factory.
     * @var StateFactoryInterface
     */
    protected $factory;


    /**
     * Constructor.
     * 
     * @param Storage\StorageInterface $storage
     * @param StateFactoryInterface $factory
     */
    public function __construct(Storage\StorageInterface $storage = null, StateFactoryInterface $factory = null)
    {
        if (null === $storage) {
            $storage = new Storage\Session();
        }
        $this->setStorage($storage);
        
        if (null === $factory) {
            $factory = new StateFactory();
        }
        $this->setFactory($factory);
    }


    /**
     * Sets the state storage.
     * 
     * @param Storage\StorageInterface $storage
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->storage = $storage;
    }


    /**
     * Returns the state storage.
     * 
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }


    /**
     * Sets the state factory.
     * 
     * @param StateFactoryInterface $factory
     */
    public function setFactory(StateFactoryInterface $factory)
    {
        $this->factory = $factory;
    }


    /**
     * Returns the state factory.
     * 
     * @return StateFactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }


    /**
     * Initializes a new state, stores it in the storage and returns it.
     * 
     * @return State
     */
    public function initState()
    {
        $state = $this->getFactory()->createState();
        $this->getStorage()->saveState($state);
        
        return $state;
    }


    /**
     * Validates the incoming state hash with the internally stored one.
     * 
     * @param string $stateHash
     * @throws Exception\StateException
     */
    public function validateState($stateHash)
    {
        $storedStateHash = null;
        if ($state = $this->getStorage()->loadState()) {
            $storedStateHash = $state->getHash();
        }
        
        /*
         * The server returned state AND there is no local state saved.
         */
        if (null === $storedStateHash && null !== $stateHash) {
            throw new Exception\InvalidLocalStateException('Invalid stored state hash - empty string');
        }
        
        /*
         * The server didn't return state AND there is a local state saved.
         */
        if (null !== $storedStateHash && null === $stateHash) {
            throw new Exception\InvalidRemoteStateException(
                sprintf("The server did not return a state hash, expected '%s'", $storedStateHash));
        }
        
        /*
         * The server returnd state AND there is a local state saved
         */
        if (null !== $storedStateHash && null !== $stateHash) {
            /*
             * FIXME - string comparison, consider security
            */
            if ($stateHash !== $storedStateHash) {
                throw new Exception\StateMismatchException(
                    sprintf("Invalid state hash returned from server '%s', expected '%s'", $stateHash, $storedStateHash));
            }
        }
    }
}