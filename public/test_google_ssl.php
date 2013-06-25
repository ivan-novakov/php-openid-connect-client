<?php
use Zend\Http\Request;

use Zend\Http\Client;
require __DIR__ . '/../init_autoload.php';

$client = _createHttpClient();

$request = new Request();
$request->setUri('https://accounts.google.com/o/oauth2/token');

$response = $client->send($request);
_dump($response->toString());


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
                //'cafile' => '/home/commanche/certs/accounts.google.ca-bundle.pem',
                'capath' => '/etc/ssl/certs'
            )
        ));
    
    return $client;
}