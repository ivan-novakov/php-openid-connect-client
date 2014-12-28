<?php

namespace InoOicClient\Oic\Authorization\State;


class StateFactory implements StateFactoryInterface
{

    /**
     * @var string
     */
    protected $hash;


    public function __construct($hash = null)
    {
        if (null === $hash) {
            $hash = bin2hex(openssl_random_pseudo_bytes(16));
        }
        $this->setHash($hash);
    }


    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }


    /**
     * @param string $secret
     */
    public function setHash($secret)
    {
        $this->hash = $secret;
    }


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\Authorization\State\StateFactoryInterface::createState()
     */
    public function createState($requestUri = null)
    {
        return new State($this->getHash(), $requestUri, time());
    }
}
