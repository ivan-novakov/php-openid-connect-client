<?php

namespace InoOicClient\Oic\Token;

use InoOicClient\Client\Authenticator\AuthenticatorFactory;
use InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface;
use InoOicClient\Oic\AbstractHttpRequestDispatcher;


class Dispatcher extends AbstractHttpRequestDispatcher
{

    /**
     * @var AuthenticatorFactoryInterface
     */
    protected $clientAuthenticatorFactory;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;


    /**
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
     * @param ResponseFactoryInterface $responseFactory
     */
    public function setResponseFactory($responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


    /**
     * @return AuthenticatorFactoryInterface
     */
    public function getClientAuthenticatorFactory()
    {
        if (! $this->clientAuthenticatorFactory instanceof AuthenticatorFactoryInterface) {
            $this->clientAuthenticatorFactory = new AuthenticatorFactory();
        }
        return $this->clientAuthenticatorFactory;
    }


    /**
     * @param AuthenticatorFactoryInterface $clientAuthenticatorFactory
     */
    public function setClientAuthenticatorFactory(AuthenticatorFactoryInterface $clientAuthenticatorFactory)
    {
        $this->clientAuthenticatorFactory = $clientAuthenticatorFactory;
    }


    public function sendTokenRequest(Request $request,\Zend\Http\Request $httpRequest = null)
    {
        $httpRequest = $this->configureHttpRequest($request, $httpRequest);
        
        try {
            $httpResponse = $this->httpClient->send($httpRequest);
        } catch (\Exception $e) {
            throw new Exception\HttpClientException(
                sprintf("Exception during HTTP request: [%s] %s", get_class($e), $e->getMessage()));
        }
        
        try {
            $responseData = $this->decodeJson($httpResponse->getContent());
        } catch (\Exception $e) {
            throw new Exception\InvalidResponseFormatException('The HTTP response does not contain valid JSON');
        }
        
        if (! $httpResponse->isSuccess()) {
            if (isset($responseData['error'])) {
                // error response exception
            }
        }
        
        try {
            $response = $this->getResponseFactory()->createResponse($responseData);
        } catch (\Exception $e) {
            // invalid response
        }
        
        return $response;
    }


    public function configureHttpRequest(Request $request,\Zend\Http\Request $httpRequest = null)
    {
        if (null === $httpRequest) {
            $httpRequest = new \Zend\Http\Request();
        }
        
        // check client info
        $clientInfo = $request->getClientInfo();
        // check endpoint
        $httpRequest->setUri($clientInfo->getTokenEndpoint());
        $httpRequest->setMethod('POST');
        $httpRequest->getPost()->fromArray(
            array(
                Param::CLIENT_ID => $clientInfo->getClientId(),
                Param::REDIRECT_URI => $clientInfo->getRedirectUri(),
                Param::GRANT_TYPE => $request->getGrantType(),
                Param::CODE => $request->getCode()
            ));
        
        $httpRequest->getHeaders()->addHeaderLine('Content-Type', 'application/x-www-form-urlencoded');
        
        $authenticator = $this->getClientAuthenticatorFactory()->createAuthenticator(
            $clientInfo->getAuthenticationInfo());
        
        $authenticator->configureHttpRequest($httpRequest);
        
        return $httpRequest;
    }


    /**
     * Decodes a JSON string to array.
     *
     * @param string $jsonData
     * @return array
     */
    public function decodeJson($jsonData)
    {
        try {
            return \Zend\Json\Json::decode($jsonData, \Zend\Json\Json::TYPE_ARRAY);
        } catch (\Exception $e) {
            throw new Exception\InvalidResponseException(
                sprintf("Error decoding JSON: [%s] %s", get_class($e), $e->getMessage()));
        }
    }
}