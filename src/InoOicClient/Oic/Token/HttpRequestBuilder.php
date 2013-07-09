<?php

namespace InoOicClient\Oic\Token;

use InoOicClient\Oic\AbstractHttpRequestBuilder;
use InoOicClient\Client\Authenticator\AuthenticatorFactory;
use InoOicClient\Client\Authenticator\AuthenticatorFactoryInterface;
use Zend\Http;
use InoOicClient\Client\ClientInfo;


class HttpRequestBuilder extends AbstractHttpRequestBuilder
{

    /**
     * @var AuthenticatorFactoryInterface
     */
    protected $clientAuthenticatorFactory;

    protected $defaultHeaders = array(
        'Content-Type' => 'application/x-www-form-urlencoded'
    );


    /**
     *
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
     *
     * @param AuthenticatorFactoryInterface $clientAuthenticatorFactory            
     */
    public function setClientAuthenticatorFactory(AuthenticatorFactoryInterface $clientAuthenticatorFactory)
    {
        $this->clientAuthenticatorFactory = $clientAuthenticatorFactory;
    }


    /**
     * Builds a HTTP request based on the token request entity.
     *
     * @param Request $request            
     * @param Http\Request $httpRequest            
     * @return Http\Request
     */
    public function buildHttpRequest(Request $request, Http\Request $httpRequest = null)
    {
        if (null === $httpRequest) {
            $httpRequest = new Http\Request();
        }
        
        $clientInfo = $request->getClientInfo();
        if (! $clientInfo instanceof ClientInfo) {
            throw new Exception\InvalidRequestException('No client info in request');
        }
        
        $endpointUri = $clientInfo->getTokenEndpoint();
        
        $httpRequest->setUri($endpointUri);
        $httpRequest->setMethod('POST');
        $httpRequest->getPost()->fromArray(
            array(
                Param::CLIENT_ID => $clientInfo->getClientId(),
                Param::REDIRECT_URI => $clientInfo->getRedirectUri(),
                Param::GRANT_TYPE => $request->getGrantType(),
                Param::CODE => $request->getCode()
            ));
        
        $headers = array_merge($this->defaultHeaders, $this->options->get(self::OPT_HEADERS, array()));
        $httpRequest->getHeaders()->addHeaders($headers);
        
        $authenticator = $this->getClientAuthenticatorFactory()->createAuthenticator($clientInfo);
        $authenticator->configureHttpRequest($httpRequest);
        
        return $httpRequest;
    }
}