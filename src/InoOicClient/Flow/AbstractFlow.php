<?php

namespace InoOicClient\Flow;

use InoOicClient\Client\ClientInfo;
use InoOicClient\Oic\Authorization;
use InoOicClient\Oic\Token;
use InoOicClient\Oic\UserInfo;
use InoOicClient\Oic\Authorization\State\Manager;
use InoOicClient\Http\ClientFactory as HttpClientFactory;
use Zend\Http;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ArrayUtils;


abstract class AbstractFlow
{

    const OPT_HTTP_CLIENT = 'http_client';

    const OPT_CLIENT_INFO = 'client_info';

    const OPT_TOKEN_DISPATCHER = 'token_dispatcher';

    /**
     * @var Parameters
     */
    protected $options;

    /**
     * @var Manager
     */
    protected $stateManager;

    /**
     * @var Authorization\Dispatcher
     */
    protected $authorizationDispatcher;

    /**
     * @var Token\Dispatcher
     */
    protected $tokenDispatcher;

    /**
     * @var UserInfo\Dispatcher
     */
    protected $userInfoDispatcher;

    /**
     * @var ClientInfo
     */
    protected $clientInfo;

    /**
     * @var HttpClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var Http\Client
     */
    protected $httpClient;


    /**
     * Constructor.
     * 
     * @param array|\Traversable $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }


    /**
     * Sets the options.
     * 
     * @param array|\Traversable $options
     * @throws \InvalidArgumentException
     */
    public function setOptions($options = array())
    {
        if (! is_array($options) && ! $options instanceof \Traversable) {
            throw new \InvalidArgumentException('The options must be array or Traversable');
        }
        
        $options = ArrayUtils::iteratorToArray($options);
        $this->options = new Parameters($options);
    }


    /**
     * Returns the options.
     * 
     * @return Parameters
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * @return Manager
     */
    public function getStateManager()
    {
        if (! $this->stateManager instanceof Manager) {
            $this->stateManager = new Manager();
        }
        return $this->stateManager;
    }


    /**
     * @param Manager $stateManager
     */
    public function setStateManager(Manager $stateManager)
    {
        $this->stateManager = $stateManager;
    }


    /**
     * @return Authorization\Dispatcher
     */
    public function getAuthorizationDispatcher()
    {
        if (! $this->authorizationDispatcher instanceof Authorization\Dispatcher) {
            $this->authorizationDispatcher = new Authorization\Dispatcher();
            $this->authorizationDispatcher->setStateManager($this->getStateManager());
        }
        return $this->authorizationDispatcher;
    }


    /**
     * @param Authorization\Dispatcher $authorizationDispatcher
     */
    public function setAuthorizationDispatcher(Authorization\Dispatcher $authorizationDispatcher)
    {
        $this->authorizationDispatcher = $authorizationDispatcher;
    }


    /**
     * @return Token\Dispatcher
     */
    public function getTokenDispatcher()
    {
        if (! $this->tokenDispatcher instanceof Token\Dispatcher) {
            $this->tokenDispatcher = new Token\Dispatcher($this->getHttpClient(), 
                $this->options->get(self::OPT_TOKEN_DISPATCHER, array()));
        }
        return $this->tokenDispatcher;
    }


    /**
     * @param Token\Dispatcher $tokenDispatcher
     */
    public function setTokenDispatcher(Token\Dispatcher $tokenDispatcher)
    {
        $this->tokenDispatcher = $tokenDispatcher;
    }


    /**
     * @return UserInfo\Dispatcher
     */
    public function getUserInfoDispatcher()
    {
        if (! $this->userInfoDispatcher instanceof UserInfo\Dispatcher) {
            $this->userInfoDispatcher = new UserInfo\Dispatcher($this->getHttpClient());
        }
        return $this->userInfoDispatcher;
    }


    /**
     * @param UserInfo\Dispatcher $userInfoDispatcher
     */
    public function setUserInfoDispatcher(UserInfo\Dispatcher $userInfoDispatcher)
    {
        $this->userInfoDispatcher = $userInfoDispatcher;
    }


    /**
     * @return ClientInfo
     */
    public function getClientInfo()
    {
        if (! $this->clientInfo instanceof ClientInfo) {
            $this->clientInfo = new ClientInfo();
            $this->clientInfo->fromArray($this->options->get(self::OPT_CLIENT_INFO, array()));
        }
        return $this->clientInfo;
    }


    /**
     * @param ClientInfo $clientInfo
     */
    public function setClientInfo(ClientInfo $clientInfo)
    {
        $this->clientInfo = $clientInfo;
    }


    /**
     * @return HttpClientFactory
     */
    public function getHttpClientFactory()
    {
        if (! $this->httpClientFactory instanceof HttpClientFactory) {
            $this->httpClientFactory = new HttpClientFactory();
        }
        return $this->httpClientFactory;
    }


    /**
     * @param HttpClientFactory $httpClientFactory
     */
    public function setHttpClientFactory(HttpClientFactory $httpClientFactory)
    {
        $this->httpClientFactory = $httpClientFactory;
    }


    /**
     * @return Http\Client
     */
    public function getHttpClient()
    {
        if (! $this->httpClient instanceof Http\Client) {
            $this->httpClient = $this->getHttpClientFactory()->createHttpClient(
                $this->options->get(self::OPT_HTTP_CLIENT, array()));
        }
        return $this->httpClient;
    }


    /**
     * @param Http\Client $httpClient
     */
    public function setHttpClient(Http\Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}