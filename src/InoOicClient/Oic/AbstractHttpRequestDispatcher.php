<?php

namespace InoOicClient\Oic;


abstract class AbstractHttpRequestDispatcher
{

    /**
     * HTTP client.
     * @var \Zend\Http\Client
     */
    protected $httpClient;


    public function __construct(\Zend\Http\Client $httpClient)
    {
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
}