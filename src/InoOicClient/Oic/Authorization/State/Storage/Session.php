<?php

namespace InoOicClient\Oic\Authorization\State\Storage;

use InoOicClient\Oic\Authorization\State\State;
use Zend\Session\Container;


class Session implements StorageInterface
{

    const VAR_AUTHORIZATION_STATE = 'authorization_state';

    /**
     * The session container.
     * @var Container
     */
    protected $container;


    /**
     * Constructor.
     * 
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        if (null === $container) {
            $container = new Container();
        }
        $this->container = $container;
    }


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\Authorization\State\Storage\StorageInterface::saveState()
     */
    public function saveState(State $state)
    {
        $this->container->offsetSet(self::VAR_AUTHORIZATION_STATE, $state);
    }


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\Authorization\State\Storage\StorageInterface::loadState()
     */
    public function loadState()
    {
        return $this->container->offsetGet(self::VAR_AUTHORIZATION_STATE);
    }
}