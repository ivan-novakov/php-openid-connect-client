<?php
require __DIR__ . '/vendor/autoload.php';

// ----------
function _dump($value)
{
    error_log(print_r($value, true));
}