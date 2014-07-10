<?php

$autoload = null;
$moduleAutoload = __DIR__ . '/../vendor/autoload.php';
$appAutoload = __DIR__ . '/../../../autoload.php';

if (file_exists($moduleAutoload)) {
    $autoload = $moduleAutoload;
} elseif (file_exists($appAutoload)) {
    $autoload = $appAutoload;
} else {
    die('No autoload available');
}

$loader = require $autoload;

define('TESTS_ROOT', __DIR__);


function _dump($value)
{
    error_log(print_r($value, true));
}