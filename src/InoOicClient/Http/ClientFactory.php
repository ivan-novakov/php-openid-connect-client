<?php

namespace InoOicClient\Http;

use Zend\Http\Client;
use Zend\Stdlib\ArrayUtils;


/**
 * Creates and configures the Zend\Http\Client.
 */
class ClientFactory implements ClientFactoryInterface
{

    /**
     * Default options.
     * @var array
     */
    protected $defaultOptions = array(
        'adapter' => 'Zend\Http\Client\Adapter\Curl',
        
        'useragent' => 'PHP OpenID Connect Client (https://github.com/ivan-novakov/php-openid-connect-client)',
        'maxredirects' => 2,
        'strictredirects' => true,
        'curloptions' => array(
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAPATH => '/etc/ssl/certs'
        )
    );


    /**
     * Sets the default options for client creation.
     * 
     * @param array|\Traversable $options
     */
    public function setDefaultOptions($options = array())
    {
        $this->defaultOptions = ArrayUtils::iteratorToArray($options);
    }


    /**
     * Returns the default options for client creation.
     * 
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }


    /**
     * Merges the provided options with the default options.
     * 
     * @param array|\Traversable $options
     * @return array
     */
    public function mergeOptions($options)
    {
        return ArrayUtils::merge($this->defaultOptions, ArrayUtils::iteratorToArray($options));
    }


    /**
     * Creates the HTTP client. The provided options are merged with the default options.
     * 
     * @param array|\Traversable $options
     * @return \Zend\Http\Client
     */
    public function createHttpClient($options = array())
    {
        $options = $this->mergeOptions($options);
        return new Client(null, $options);
    }
}