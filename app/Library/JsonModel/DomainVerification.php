<?php

namespace Acelle\Library\JsonModel;

use Exception;

class DomainVerification extends Base
{
    protected $data;
    protected $schema = [
        'identity' => [
            'type' => 'TXT',
            'name' => 'example.com',
            'value' => 'brevo-code:424d0b6273f43905ed0ca3e34f000816',
        ],
        'dkim' => [
            [
                'type' => 'TXT',
                'name' => 'mail._domainkey.example.com',
                'value' => 'k=rsa;p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDeMVIzrCa3T14...',
            ]
        ],
        'spf' => [
            [
                'type' => 'TXT',
                'name' => 'example.com',
                'value' => 'v=spf1 include:sendinblue.com ~all'
            ]
        ],
        'results' => [
            'identity' => false,
            'dkim' => false,
            'spf' => false,
        ],
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
        $requiredKeys = [ 'identity', 'dkim', 'results' ];

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
