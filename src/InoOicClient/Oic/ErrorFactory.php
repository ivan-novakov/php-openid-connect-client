<?php

namespace InoOicClient\Oic;


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
}