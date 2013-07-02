<?php

namespace InoOicClient\Oic;

use InoOicClient\Oic\Token\Param;


class ErrorFactory implements ErrorFactoryInterface
{


    /**
     * {@inhertidoc}
     * @see \InoOicClient\Oic\ErrorFactoryInterface::createError()
     */
    public function createError($code, $description = null, $uri = null)
    {
        $error = new Error();
        $error->setCode($code);
        
        if (null !== $description) {
            $error->setDescription($description);
        }
        
        if (null !== $uri) {
            $error->setUri($uri);
        }
        
        return $error;
    }


    /**
     * {@inheritdoc}
     * @see \InoOicClient\Oic\ErrorFactoryInterface::createErrorFromArray()
     */
    public function createErrorFromArray(array $data)
    {
        if (! isset($data[Param::ERROR])) {
            throw new Exception\InvalidErrorCodeException('Empty error code');
        }
        $code = $data[Param::ERROR];
        
        $description = isset($data[Param::ERROR_DESCRIPTION]) ? $data[Param::ERROR_DESCRIPTION] : null;
        $uri = isset($data[Param::ERROR_URI]) ? $data[Param::ERROR_URI] : null;
        
        return $this->createError($code, $description, $uri);
    }
}