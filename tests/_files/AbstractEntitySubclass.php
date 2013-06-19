<?php

use InoOicClient\Entity\AbstractEntity;


class AbstractEntitySubclass extends AbstractEntity
{

    protected $allowedProperties = array(
        'foo',
        'another'
    );
}