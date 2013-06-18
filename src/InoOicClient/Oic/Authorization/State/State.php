<?php

namespace InoOicClient\Oic\Authorization\State;

use InoOicClient\Entity\AbstractEntity;


/**
 * Container for the state information of the authorization request.
 * 
 * @method void setHash(string $hash)
 * @method void setRequestUri(string $requestUri)
 * @method void setCtime(string $ctime)
 * 
 * @method string getHash()
 * @method string getRequestUri()
 * @method string getCtime()
 */
class State extends AbstractEntity
{


    /**
     * Constructor.
     * 
     * @param string $state
     * @param string $requestUri
     * @param string $ctime
     */
    public function __construct($hash, $requestUri, $ctime = null)
    {
        $this->setHash($hash);
        $this->setRequestUri($requestUri);
        $this->setCtime($ctime);
    }

}