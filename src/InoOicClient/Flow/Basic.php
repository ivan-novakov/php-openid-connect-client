<?php

namespace InoOicClient\Flow;

use InoOicClient\Oic\Authorization;
use InoOicClient\Oic\Token;
use InoOicClient\Oic\UserInfo;


class Basic extends AbstractFlow
{


    /**
     * Returns the authorization request URI.
     * 
     * @param string|array $scope
     * @param string $responseType
     * @return string
     */
    public function getAuthorizationRequestUri($scope = 'openid', $responseType = 'code')
    {
        $authorizationRequest = $this->createAuthorizationRequest($scope, $responseType);
        return $this->getAuthorizationDispatcher()->createAuthorizationRequestUri($authorizationRequest);
    }


    /**
     * Returns the authorization code received from the server after successful user login.
     * 
     * @return string
     */
    public function getAuthorizationCode()
    {
        $authorizationResponse = $this->getAuthorizationDispatcher()->getAuthorizationResponse();
        return $authorizationResponse->getCode();
    }


    /**
     * Requests the server for access token and returns it.
     * 
     * @param string $authorizationCode
     * @return string
     */
    public function getAccessToken($authorizationCode)
    {
        $tokenRequest = $this->createTokenRequest($authorizationCode);
        $tokenResponse = $this->getTokenDispatcher()->sendTokenRequest($tokenRequest);
        return $tokenResponse->getAccessToken();
    }


    /**
     * Requests the server for user info based on the provided token and returns the claims in the response.
     * 
     * @param string $accessToken
     * @return array
     */
    public function getUserInfo($accessToken)
    {
        $userInfoRequest = $this->createUserInfoRequest($accessToken);
        $userInfoResponse = $this->getUserInfoDispatcher()->sendUserInfoRequest($userInfoRequest);
        return $userInfoResponse->getClaims();
    }


    public function process()
    {
        $authorizationCode = $this->getAuthorizationCode();
        $accessToken = $this->getAccessToken($authorizationCode);
        return $this->getUserInfo($accessToken);
    }


    public function createAuthorizationRequest($scope = 'openid', $responseType = 'code')
    {
        return new Authorization\Request($this->getClientInfo(), $responseType, $scope);
    }


    public function createTokenRequest($authorizationCode)
    {
        $tokenRequest = new Token\Request();
        $tokenRequest->setClientInfo($this->getClientInfo());
        $tokenRequest->setCode($authorizationCode);
        $tokenRequest->setGrantType('authorization_code');
        
        return $tokenRequest;
    }


    public function createUserInfoRequest($accessToken)
    {
        $userInfoRequest = new UserInfo\Request();
        $userInfoRequest->setClientInfo($this->getClientInfo());
        $userInfoRequest->setAccessToken($accessToken);
        
        return $userInfoRequest;
    }
}