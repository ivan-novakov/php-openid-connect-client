<?php

namespace InoOicClient\Server;

use InoOicClient\Entity\AbstractEntity;


/**
 * Server information container.
 * 
 * @method void setName(string $name)
 * @method void setDescription(string $description)
 * @method void setAuthorizationEndpoint(string $authorizationEndpoint)
 * @method void setTokenEndpoint(string $tokenEndpoint)
 * @method void setUserInfoEndpoint(stirng $userInfoEndpoint)
 * 
 * @method string getName()
 * @method string getDescription()
 * @method string getAuthorizationEndpoint()
 * @method string getTokenEndpoint()
 * @method string getUserInfoEndpoint()
 */
class ServerInfo extends AbstractEntity
{

    protected $allowedProperties = array(
        'name',
        'description',
        'authorization_endpoint',
        'token_endpoint',
        'user_info_endpoint'
    );
}