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


    /**
     * Processes the "token" part of the flow:
     * - retrieves the authorization code
     * - retrieves the access token
     * - retrieves user info
     * 
     * @return array
     */
    public function process()
    {
        try {
            $authorizationCode = $this->getAuthorizationCode();
        } catch (\Exception $e) {
            throw new Exception\AuthorizationException(
                sprintf("Exception during authorization: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
        
        try {
            $accessToken = $this->getAccessToken($authorizationCode);
        } catch (\Exception $e) {
            throw new Exception\TokenRequestException(
                sprintf("Exception during token request: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
        
        try {
            return $this->getUserInfo($accessToken);
        } catch (\Exception $e) {
            throw new Exception\UserInfoRequestException(
                sprintf("Exception during user info request: [%s] %s", get_class($e), $e->getMessage()), null, $e);
        }
    }


    /**
     * Creates authorization request.
     * 
     * @param string|array $scope
     * @param string|array $responseType
     * @return Authorization\Request
     */
    public function createAuthorizationRequest($scope = 'openid', $responseType = 'code')
    {
        return new Authorization\Request($this->getClientInfo(), $responseType, $scope);
    }


    /**
     * Creates token request.
     * 
     * @param string $authorizationCode
     * @return Token\Request
     */
    public function createTokenRequest($authorizationCode)
    {
        $tokenRequest = new Token\Request();
        $tokenRequest->setClientInfo($this->getClientInfo());
        $tokenRequest->setCode($authorizationCode);
        $tokenRequest->setGrantType('authorization_code');
        
        return $tokenRequest;
    }


    /**
     * Creates user info request.
     * 
     * @param string $accessToken
     * @return UserInfo\Request
     */
    public function createUserInfoRequest($accessToken)
    {
        $userInfoRequest = new UserInfo\Request();
        $userInfoRequest->setClientInfo($this->getClientInfo());
        $userInfoRequest->setAccessToken($accessToken);
        
        return $userInfoRequest;
    }
}