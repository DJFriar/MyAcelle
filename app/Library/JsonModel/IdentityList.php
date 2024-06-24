<?php

namespace Acelle\Library\JsonModel;

use Exception;

// Do not extend base
class IdentityList
{
    public $data = [];

    public function __construct()
    {
        // Nothing here
    }

    public function addIdentity(Identity $record)
    {
        $name = $record->get('Name');
        $this->data[$name] = $record;
    }

    public function getIdentityByName($name): ?Identity
    {
        if (!array_key_exists($name, $this->data)) {
            return null;
        }

        return $this->data[$name];
    }

    public function getAllNames()
    {
        return array_keys($this->data);
    }

    public function get()
    {
        $json = [];
        foreach ($this->data as $key => $identity) {
            $json[$key] = $identity->get();
        }

        return $json;
    }
}
