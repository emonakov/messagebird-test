<?php

namespace Messagebird\Model;


interface ModelInterface
{
    public function getId();

    public function load($id, $field = 'id');

    public function save();
}