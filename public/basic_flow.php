<?php

use InoOicClient\Flow\Basic;

require __DIR__ . '/../init_autoload.php';

$config = require __DIR__ . '/config.php';

$flow = new Basic($config);

if (! isset($_GET['redirect'])) {
    
    try {
        $uri = $flow->getAuthorizationRequestUri();
    } catch (\Exception $e) {
        _dump("$e");
        printf("Exception during authorization URI creation: [%s] %s", get_class($e), $e->getMessage());
    }
} else {
    
    try {
        $userInfo = $flow->process();
        printf("<pre>%s</pre>", print_r($userInfo));
    } catch (\Exception $e) {
        _dump("$e");
        printf("Exception during user authentication: [%s] %s", get_class($e), $e->getMessage());
    }
}