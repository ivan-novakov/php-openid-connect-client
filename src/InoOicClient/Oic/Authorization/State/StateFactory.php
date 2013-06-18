<?php

namespace InoOicClient\Oic\Authorization\State;

use Zend\Crypt\Hash;


class StateFactory implements StateFactoryInterface
{

    protected $algo = 'sha256';

    protected $secret = 'some random data';


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\Authorization\State\StateFactoryInterface::createState()
     */
    public function createState($requestUri = null)
    {
        return new State($this->generateHash(), $requestUri, time());
    }


    protected function generateHash()
    {
        $data = $this->secret . microtime(true);
        return Hash::compute($this->algo, $data);
    }
}
