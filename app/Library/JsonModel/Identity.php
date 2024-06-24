<?php

namespace Acelle\Library\JsonModel;

use Exception;

class Identity extends Base
{
    protected $data;

    protected $schema = [
        'Name' => 'domain.name',
        'VerificationStatus' => true,
        'UserId' => 1769,
        'UserName' => 'Napoléon Bonaparte',
    ];

    public function __construct($data)
    {
        // Throw an exception if failing
        $this->validate($data);

        // OK
        $this->data = $data;
    }

    public function validate($data)
    {
        $requiredKeys = [ 'Name', 'VerificationStatus' ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new Exception(sprintf('Invalid JSON structure: key "%s" missing', $key));
            }

            // Check type
            $requiredType = gettype($this->schema[$key]);
            $dataType = gettype($data[$key]);
            if ($dataType != $requiredType) {
                throw new Exception(sprintf('Invalid type for key "%s", required type is "%s", got "%s"', $key, $requiredType, $dataType));
            }
        }
    }
}
