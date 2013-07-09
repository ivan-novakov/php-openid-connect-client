<?php

namespace InoOicClient\Oic;

use InoOicClient\Json\Coder;
use InoOicClient\Oic\Exception\HttpClientException;
use Zend\Http;


/**
 * Abstract dispatcher class for accessing server endpoints such as /token and /userinfo.
 */
abstract class AbstractHttpRequestDispatcher
{

    /**
     * HTTP client.
     * @var Http\Client
     */
    protected $httpClient;

    /**
     * JSON coder/decoder.
     * @var Coder
     */
    protected $jsonCoder;

    /**
     * @var Http\Request
     */
    protected $lastHttpRequest;

    /**
     * @var Http\Response
     */
    protected $lastHttpResponse;


    /**
     * Constructor.
     * 
     * @param Http\Client $httpClient
     */
    public function __construct(Http\Client $httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = new Http\Client();
        }
        $this->setHttpClient($httpClient);
    }


    /**
     * @return Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }


    /**
     * @param Http\Client $httpClient
     */
    public function setHttpClient(Http\Client $httpClient)
    {
        $this->httpClient = $httpClient;
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


    /**
     * Sends the HTTP request and returns the response.
     * 
     * @param Http\Request $httpRequest
     * @throws HttpClientException
     * @return Http\Response
     */
    public function sendHttpRequest(Http\Request $httpRequest)
    {
        $this->setLastHttpRequest($httpRequest);
        
        try {
            $httpResponse = $this->httpClient->send($httpRequest);
        } catch (\Exception $e) {
            throw new HttpClientException(
                sprintf("Exception during HTTP request: [%s] %s", get_class($e), $e->getMessage()));
        }
        
        $this->setLastHttpResponse($httpResponse);
        return $httpResponse;
    }


    /**
     * Returns the last HTTP request sent to the server.
     * 
     * @return Http\Request
     */
    public function getLastHttpRequest()
    {
        return $this->lastHttpRequest;
    }


    /**
     * Returns the last HTTP response sent from the server.
     * 
     * @return Http\Response
     */
    public function getLastHttpResponse()
    {
        return $this->lastHttpResponse;
    }


    /**
     * Sets the last HTTP request sent to the server.
     * 
     * @param Http\Request $httpRequest
     */
    protected function setLastHttpRequest(Http\Request $httpRequest)
    {
        $this->lastHttpRequest = $httpRequest;
    }


    /**
     * Sets the last HTTP response sent from the server.
     * 
     * @param Http\Response $httpResponse
     */
    protected function setLastHttpResponse(Http\Response $httpResponse)
    {
        $this->lastHttpResponse = $httpResponse;
    }
}