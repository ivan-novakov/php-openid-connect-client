<?php

namespace InoOicClient\Oic\UserInfo;

use Zend\Http;
use InoOicClient\Json\Coder;
use InoOicClient\Oic\ErrorFactoryInterface;
use InoOicClient\Oic\Exception\InvalidResponseFormatException;
use InoOicClient\Oic\Exception\HttpAuthenticateException;
use InoOicClient\Oic\Exception\HttpErrorStatusException;
use InoOicClient\Oic\AbstractResponseHandler;


/**
 * Parses the userinfo response and creates the corresponding userinfo response entity or an error entity in case
 * of an error response or throws a specific exception if there is something different from the expected.
 */
class ResponseHandler extends AbstractResponseHandler
{

    protected $wwwAuthenticateHeaderName = 'WWW-Authenticate';

    protected $authSchemeName = 'bearer';

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
     * Parses the HTTP response from a userinfo request.
     * 
     * @param Http\Response $httpResponse
     * @throws HttpAuthenticateException
     * @throws HttpErrorStatusException
     * @throws InvalidResponseFormatException
     * @throws Exception\InvalidResponseException
     */
    public function handleResponse(Http\Response $httpResponse)
    {
        if (! $httpResponse->isSuccess()) {
            
            $statusCode = $httpResponse->getStatusCode();
            if (401 === $statusCode && ($authenticateHeader = $httpResponse->getHeaders()
                ->get($this->wwwAuthenticateHeaderName)
                ->current())) {
                
                $params = $this->parseAuthenticateHeaderValue($authenticateHeader->getFieldValue());
                if (isset($params['error'])) {
                    $this->setError($this->getErrorFactory()
                        ->createErrorFromArray($params));
                    return;
                }
                
                throw new HttpAuthenticateException(
                    sprintf("Missing error information in WWW-Authenticate header: %s", 
                        $authenticateHeader->getFieldValue()));
            }
            
            throw new HttpErrorStatusException(sprintf("Error status response from server: %s", $statusCode));
        }
        
        try {
            $responseData = $this->getJsonCoder()->decode($httpResponse->getBody());
        } catch (\Exception $e) {
            throw new InvalidResponseFormatException('The HTTP response does not contain valid JSON', null, $e);
        }
        
        try {
            $this->response = $this->getResponseFactory()->createResponse($responseData);
        } catch (\Exception $e) {
            throw new Exception\InvalidResponseException(
                sprintf("Invalid response: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
    }


    /**
     * Parses the "WWW-Authenticate" header and returns the corresponding params as array.
     * 
     * @param string $rawValue
     * @throws InvalidResponseFormatException
     * @return array
     */
    public function parseAuthenticateHeaderValue($rawValue)
    {
        $params = array();
        
        $fields = explode(' ', $rawValue, 2);
        $authScheme = $fields[0];
        
        if ($this->authSchemeName !== strtolower($authScheme)) {
            throw new InvalidResponseFormatException(
                sprintf("Invalid auth scheme in WWW-Authenticate header: %s", $authScheme));
        }
        
        if (isset($fields[1])) {
            $params = $this->parseAuthenticationHeaderParams($fields[1]);
        }
        
        return $params;
    }


    /**
     * Parses a key-value serialized string, for example: key1=value1, key2=value2
     * 
     * @param string $paramsString
     * @return array
     */
    public function parseAuthenticationHeaderParams($paramsString)
    {
        $params = array();
        $parts = explode(',', $paramsString);
        foreach ($parts as $keyValueString) {
            $keyValueParts = explode('=', $keyValueString);
            $key = trim($keyValueParts[0]);
            if (! isset($keyValueParts[1]) || ! $keyValueParts[1]) {
                continue;
            }
            $value = str_replace('"', '', trim($keyValueParts[1]));
            $params[$key] = $value;
        }
        
        return $params;
    }
}