<?php
use InoOicClient\Flow\Basic;

require __DIR__ . '/../../init_autoload.php';

$config = require __DIR__ . '/config.php';

$flow = new Basic($config);

if (! isset($_GET['redirect'])) {

    try {
        $uri = $flow->getAuthorizationRequestUri('user:email');
        _dump($uri);
        printf("<pre>%s</pre><br>", $uri);
        printf("<a href=\"%s\">Login</a>", $uri);
    } catch (\Exception $e) {
        _dump("$e");
        printf("Exception during authorization URI creation: [%s] %s", get_class($e), $e->getMessage());
    }
} else {

    try {
        $userInfo = $flow->process();
        printf("<pre>%s</pre>", print_r($userInfo, true));
    } catch (\Exception $e) {
        _dump("$e");
        printf("Exception during user authentication: <br><pre>%s</pre>", $e);
        echo "<br>";
        _dump($flow->getTokenDispatcher()->getLastHttpResponse()->getBody());
    }
}