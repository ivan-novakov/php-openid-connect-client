<?php

namespace InoOicClient\Oic;

use InoOicClient\Json\Coder;


abstract class AbstractHttpRequestDispatcher
{

    /**
     * HTTP client.
     * @var \Zend\Http\Client
     */
    protected $httpClient;

    /**
     * @var ErrorFactoryInterface
     */
    protected $errorFactory;

    /**
     * JSON coder/decoder.
     * @var Coder
     */
    protected $jsonCoder;


    public function __construct(\Zend\Http\Client $httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = new \Zend\Http\Client();
        }
        $this->setHttpClient($httpClient);
    }


    /**
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }


    /**
     * @param \Zend\Http\Client $httpClient
     */
    public function setHttpClient(\Zend\Http\Client $httpClient)
    {
        $this->httpClient = $httpClient;
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
     * @return Coder $jsonCoder
     */
    public function getJsonCoder()
    {
        if (! $this->jsonCoder instanceof Coder) {
            $this->jsonCoder = new Coder();
        }
        return $this->jsonCoder;
    }


    /**
     * @param Coder $jsonCoder
     */
    public function setJsonCoder(Coder $jsonCoder)
    {
        $this->jsonCoder = $jsonCoder;
    }
}