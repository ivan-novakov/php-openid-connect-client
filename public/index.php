<?php
use InoOicClient\Oic\Authorization;
use InoOicClient\Oic\Token;
use InoOicClient\Server\ServerInfo;
use InoOicClient\Client\ClientInfo;
use InoOicClient\Oic\Authorization\State\Manager;
use InoOicClient\Oic\Authorization\Exception\ErrorResponseException;
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
    
    $request = new Authorization\Request($clientInfo, 'code', 'openid');
    
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
        $tokenResponse = $tokenDispatcher->sendTokenRequest($tokenRequest);
        _dump($tokenResponse);
        
        printf("Access token: %s<br>", $tokenResponse->getAccessToken());
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
    $adapter = new Client\Adapter\Socket();
    $client = new Client();
    $client->setOptions(array(
        'maxredirects' => 2,
        'strictredirects' => true
    ));
    $client->setAdapter($adapter);
    
    $adapter->setStreamContext(
        array(
            'ssl' => array(
                'cafile' => '/home/commanche/certs/accounts.google.ca-bundle.pem'
            )
        ));
    
    return $client;
}
