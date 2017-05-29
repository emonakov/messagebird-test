<?php

namespace Messagebird\Model;


interface ModelInterface
{
    public function load($id, $field = 'id');

    public function save();
}