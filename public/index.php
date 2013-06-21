<?php
use InoOicClient\Oic\Authorization;
use InoOicClient\Server\ServerInfo;
use InoOicClient\Client\ClientInfo;
use InoOicClient\Oic\Authorization\State\Manager;
use InoOicClient\Oic\Authorization\Exception\ErrorResponseException;
use InoOicClient\Oic\Authorization\Exception\StateException;
require __DIR__ . '/../init_autoload.php';

$config = array(
    
    'server_info' => array(
        'authorizationEndpoint' => 'https://hroch.cesnet.cz/devel/oic/server/oic/authorize'
    ),
    
    'client_info' => array(
        'client_id' => 'test-chabruz-client',
        'redirect_uri' => 'https://chabruz.feld.cvut.cz/devel/oic/client?redirect'
    )
);

$stateManager = new Manager();

$dispatcher = new Authorization\Dispatcher();
$dispatcher->setStateManager($stateManager);

if (! isset($_GET['redirect'])) {
    
    $serverInfo = new ServerInfo();
    $serverInfo->fromArray($config['server_info']);
    
    $clientInfo = new ClientInfo();
    $clientInfo->fromArray($config['client_info']);
    
    $request = new Authorization\Request($clientInfo, $serverInfo, 'code', 'openid');
    
    $uri = $dispatcher->createAuthorizationRequestUri($request);
    
    _dump($uri);
    
    printf("<pre>%s</pre><br>", $uri);
    printf("<a href=\"%s\">Login</a>", $uri);
} else {
    
    try {
        $response = $dispatcher->getAuthorizationResponse();
    } catch (ErrorResponseException $e) {
        $error = $e->getError();
        printf("Error: %s<br>Description: %s<br>", $error->getCode(), $error->getDescription());
    } catch (StateException $e) {
        printf('state exception');
    } catch (\Exception $e) {
        _dump("$e");
    }
}
