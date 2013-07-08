<?php
use InoOicClient\Http\ClientFactory;

use InoOicClient\Oic\Authorization;
use InoOicClient\Oic\Token;
use InoOicClient\Oic\UserInfo;
use InoOicClient\Client\ClientInfo;
use InoOicClient\Oic\Authorization\State\Manager;
use InoOicClient\Oic\Exception\ErrorResponseException;
use InoOicClient\Oic\Authorization\Exception\StateException;
use Zend\Http\Client;
require __DIR__ . '/../init_autoload.php';

$config = require __DIR__ . '/config.php';

$clientInfo = new ClientInfo();
$clientInfo->fromArray($config['client_info']);

$stateManager = new Manager();

$dispatcher = new Authorization\Dispatcher();
$dispatcher->setStateManager($stateManager);

if (! isset($_GET['redirect'])) {
    
    $request = new Authorization\Request($clientInfo, 'code', 'openid profile email');
    
    $uri = $dispatcher->createAuthorizationRequestUri($request);
    
    _dump($uri);
    
    printf("<pre>%s</pre><br>", $uri);
    printf("<a href=\"%s\">Login</a>", $uri);
} else {
    
    try {
        $response = $dispatcher->getAuthorizationResponse();
        printf("OK<br>Code: %s<br>State: %s<br>", $response->getCode(), $response->getState());
        
        $tokenRequest = new Token\Request();
        $tokenRequest->fromArray(
            array(
                'client_info' => $clientInfo,
                'code' => $response->getCode(),
                'grant_type' => 'authorization_code'
            ));
        
        $httpClient = _createHttpClient();
        $tokenDispatcher = new Token\Dispatcher($httpClient);
        
        try {
            $tokenResponse = $tokenDispatcher->sendTokenRequest($tokenRequest);
            _dump($tokenResponse);
            printf("Access token: %s<br>", $tokenResponse->getAccessToken());
            
            $userInfoRequest = new UserInfo\Request();
            $userInfoRequest->setAccessToken($tokenResponse->getAccessToken());
            $userInfoRequest->setClientInfo($clientInfo);
            
            $userInfoDispatcher = new UserInfo\Dispatcher($httpClient);
            
            try {
                $userInfoResponse = $userInfoDispatcher->sendUserInfoRequest($userInfoRequest);
                _dump($userInfoResponse->getClaims());
                printf("User info: %s", 
                    \Zend\Json\Json::encode($userInfoResponse->getClaims(), \Zend\Json\Json::TYPE_ARRAY));
            } catch (\Exception $e) {
                printf("Error: [%s] %s<br>", get_class($e), $e->getMessage());
                _dump("$e");
            }
            _dump($httpClient);
        } catch (\Exception $e) {
            printf("Error: [%s] %s<br>", get_class($e), $e->getMessage());
            _dump("$e");
        }
    } catch (ErrorResponseException $e) {
        $error = $e->getError();
        printf("Error: %s<br>Description: %s<br>", $error->getCode(), $error->getDescription());
    } catch (StateException $e) {
        printf("State exception:<br>%s", $e);
    } catch (\Exception $e) {
        _dump("$e");
    }
}


function _createHttpClient()
{
    $httpClientFactory = new ClientFactory();
    return $httpClientFactory->createHttpClient();
}
