<?php

namespace InoOicClient\Oic\Token;

use InoOicClient\Entity\AbstractEntity;


/**
 * Token response.
 * 
 * @method void setAccessToken(string $accessToken)
 * @method void setTokenType(string $tokenType)
 * @method void setRefreshToken(string $refreshToken)
 * @method void setExpiresIn(integer $expiresIn)
 * @method void setScope(string $scope)
 * @method void setIdToken(mixed $idToken) not implemented
 * 
 * @method string getAccessToken()
 * @method string getTokenType()
 * @method string getRefreshToken()
 * @method integer getExpiresIn()
 * @method string getScope()
 * @method mixed getIdToken() not implemented
 */
class Response extends AbstractEntity
{

    protected $allowedProperties = array(
        Param::ACCESS_TOKEN,
        Param::TOKEN_TYPE,
        Param::REFRESH_TOKEN,
        Param::EXPIRES_IN,
        Param::ID_TOKEN,
        Param::SCOPE,
    );
}