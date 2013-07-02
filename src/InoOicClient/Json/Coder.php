<?php

namespace InoOicClient\Json;

use Zend\Json\Json;

/**
 * The class handles JSON encoding/decoding.
 */
class Coder
{


    /**
     * Decodes a JSON string into an array.
     * 
     * @param string $jsonString
     * @throws Exception\DecodeException
     * @return array
     */
    public function decode($jsonString)
    {
        try {
            return Json::decode($jsonString, Json::TYPE_ARRAY);
        } catch (\Exception $e) {
            throw new Exception\DecodeException(
                sprintf("JSON decode exception: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
    }
}