<?php

namespace InoOicClient\Oic\Token;

use InoOicClient\Oic\ErrorFactory;
use InoOicClient\Oic\Exception\ErrorResponseException;
use InoOicClient\Client\Authenticator\AuthenticatorFactory;
use InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface;
use InoOicClient\Oic\AbstractHttpRequestDispatcher;
use InoOicClient\Oic\ErrorFactoryInterface;


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
     * @var ErrorFactoryInterface
     */
    protected $errorFactory;


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


    public function sendTokenRequest(Request $request,\Zend\Http\Request $httpRequest = null)
    {
        try {
            $httpRequest = $this->configureHttpRequest($request, $httpRequest);
        } catch (\Exception $e) {
            throw new Exception\InvalidRequestException(
                sprintf("Invalid request: [%s] %s", get_class($e), $e->getMessage()));
        }
        
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
            if (isset($responseData[Param::ERROR])) {
                $code = $responseData[Param::ERROR];
                $description = isset($responseData[Param::ERROR_DESCRIPTION]) ? $responseData[Param::ERROR_DESCRIPTION] : null;
                $uri = isset($responseData[Param::ERROR_URI]) ? $responseData[Param::ERROR_URI] : null;
                $error = $this->getErrorFactory()->createError($code, $description, $uri);
                
                throw new ErrorResponseException($error);
            }
        }
        
        try {
            $response = $this->getResponseFactory()->createResponse($responseData);
        } catch (\Exception $e) {
            throw new Exception\InvalidResponseException(
                sprintf("Invalid response: [%s] %s", get_class($e), $e->getMessage()));
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
        
        $authenticator = $this->getClientAuthenticatorFactory()->createAuthenticator($clientInfo);
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