<?php

namespace InoOicClient\Oic\Token;

use InoOicClient\Oic\AbstractResponseHandler;
use InoOicClient\Oic\Exception\HttpErrorStatusException;
use InoOicClient\Oic\Exception\InvalidResponseFormatException;
use InoOicClient\Oic\ErrorFactoryInterface;
use InoOicClient\Json\Coder;


class ResponseHandler extends AbstractResponseHandler
{

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var Response
     */
    protected $response;


    /**
     * Constructor.
     *
     * @param Coder $jsonCoder
     * @param ResponseFactoryInterface $responseFactory
     * @param ErrorFactoryInterface $errorFactory
     */
    public function __construct(Coder $jsonCoder = null, ResponseFactoryInterface $responseFactory = null, 
        ErrorFactoryInterface $errorFactory = null)
    {
        if (null !== $jsonCoder) {
            $this->setJsonCoder($jsonCoder);
        }
        
        if (null !== $responseFactory) {
            $this->setResponseFactory($responseFactory);
        }
        
        if (null !== $errorFactory) {
            $this->setErrorFactory($errorFactory);
        }
    }


    /**
     * @return ResponseFactoryInterface $responseFactory
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
    public function setResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


    /**
     * Returns the userinfo response entity.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\AbstractResponseHandler::handleResponse()
     */
    public function handleResponse(\Zend\Http\Response $httpResponse)
    {
        $responseData = null;
        $decodeException = null;
        
        try {
            $responseData = $this->getJsonCoder()->decode($httpResponse->getBody());
        } catch (\Exception $e) {
            $decodeException = $e;
        }
        
        if (! $httpResponse->isSuccess()) {
            if (isset($responseData[Param::ERROR])) {
                $error = $this->getErrorFactory()->createErrorFromArray($responseData);
                $this->setError($error);
                return;
            } else {
                throw new HttpErrorStatusException(
                    sprintf("Error code '%d' from server", $httpResponse->getStatusCode()));
            }
        }
        
        if (null !== $decodeException) {
            throw new InvalidResponseFormatException('The HTTP response does not contain valid JSON', null, 
                $decodeException);
        }
        
        try {
            $this->response = $this->getResponseFactory()->createResponse($responseData);
        } catch (\Exception $e) {
            throw new Exception\InvalidResponseException(
                sprintf("Invalid response: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
    }
}