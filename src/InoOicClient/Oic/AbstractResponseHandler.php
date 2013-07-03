<?php

namespace InoOicClient\Oic;

use InoOicClient\Json\Coder;


abstract class AbstractResponseHandler
{

    /**
     * @var Coder
     */
    protected $jsonCoder;

    /**
     * @var ErrorFactoryInterface
     */
    protected $errorFactory;

    /**
     * @var Error
     */
    protected $error;


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
     * @return ErrorFactoryInterface $errorFactory
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
    public function setErrorFactory(ErrorFactoryInterface $errorFactory)
    {
        $this->errorFactory = $errorFactory;
    }


    /**
     * @param Error $error
     */
    public function setError(Error $error)
    {
        $this->error = $error;
    }


    /**
     * Returns true, if there is an error.
     *
     * @return boolean
     */
    public function isError()
    {
        return (null !== $this->error);
    }


    /**
     * Returns the error.
     *
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * Parses the HTTP response.
     * 
     * @param \Zend\Http\Response $httpResponse
     */
    abstract public function handleResponse(\Zend\Http\Response $httpResponse);
}