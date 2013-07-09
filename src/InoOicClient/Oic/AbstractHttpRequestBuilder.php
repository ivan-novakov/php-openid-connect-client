<?php

namespace InoOicClient\Oic;

use Zend\Stdlib\ArrayUtils;

use Zend\Stdlib\Parameters;


abstract class AbstractHttpRequestBuilder
{

    const OPT_HEADERS = 'headers';

    /**
     * @var Parameters
     */
    protected $options;


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
    public function setOptions($options)
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
}