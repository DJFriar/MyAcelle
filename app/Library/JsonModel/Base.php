<?php

namespace Acelle\Library\JsonModel;

class Base
{
    protected $data;

    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->data;
        } else {
            return $this->data[$key];
        }
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
