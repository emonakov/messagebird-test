<?php

namespace Messagebird\Model;

class ModelFactory
{
    public static function create($class)
    {
        return new $class;
    }
}