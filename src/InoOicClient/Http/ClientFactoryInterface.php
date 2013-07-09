<?php

namespace InoOicClient\Http;


interface ClientFactoryInterface
{


    public function createHttpClient($options = array());
}