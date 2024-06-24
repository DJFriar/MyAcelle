<?php

namespace Acelle\Library;

use Exception;
use Acelle\Model\MailList;
use DB;

class MailListFieldMapping
{
    public $mapping = [];
    public $list;

    // Use lower-case here
    public $preservedFields = ['tags'];

    private function __construct($mapping, $list)
    {
        $this->mapping = $mapping;
        $this->list = $list;
    }

    public function generateFieldNameFromId(int $id)
    {
        return "field_{$id}";
    }

    public static function parse(array $mapping, MailList $list)
    {
        self::validate($mapping, $list);

        $mapObj = new self($mapping, $list);

        return $mapObj;
    }

    public function getHeaders()
    {
        return array_keys($this->mapping);
    }

    public static function validate($map, $list)
    {
        // Check if EMAIL (required) is included in the map
        $fieldIds = array_values($map);
        $emailFieldId = $list->getEmailField()->id;

        if (!in_array($emailFieldId, $fieldIds)) {
            throw new Exception(trans('messages.list.import.errors.email_missing'));
        }

        // Check if field id is valid
        foreach ($map as $header => $fieldId) {
            if (!$list->fields()->where('id', $fieldId)->exists()) {
                throw new Exception(trans('messages.list.import.errors.field_id_invalid', ['id' => $fieldId, 'header' => $header, 'list' => $list->name]));
            }
        }
    }


    // Transform a record like:   [ 'First Name' => 'Joe', 'Email' => 'joe@america.us', 'tags' => 'SOME TAGS', 'others' => 'Others' ]
    // to something like
    //
    //     [ 'field_1' => 'Joe', 'field_2' => 'joe@america.us',  'tags' => 'SOME TAGS']
    //
    // i.e. Change header based on mapped field, remove other fields (not in map)
    public function updateRecordHeaders($r)
    {
        // IMPORTANT: 'tags' must be lower-case
        // Extract the relevant fields, including preserved fields
        $selectedFields = array_merge($this->getHeaders(), $this->preservedFields);
        $record = array_only($r, $selectedFields);

        // Change original header to mapped field name
        foreach ($this->mapping as $header => $fieldId) {
            $fieldName = $this->generateFieldNameFromId($fieldId);
            $record[$fieldName] = $record[$header];
            unset($record[$header]);
        }

        return $record;
    }

    public function createTmpTableFromMapping()
    {
        // create a temporary table containing the input subscribers
        $tmpTable = table('__tmp_subscribers');
        $emailFieldId = $this->list->getEmailField()->id;
        $emailFieldName = $this->generateFieldNameFromId($emailFieldId);

        // @todo: hard-coded charset and COLLATE
        $tmpFields = array_map(function ($fieldId) {
            $fieldName = $this->generateFieldNameFromId($fieldId);
            return "`{$fieldName}` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        }, $this->mapping);

        foreach ($this->preservedFields as $field) {
            $tmpFields[] = "`{$field}` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        }

        $tmpFields = implode(',', $tmpFields);

        // Drop table, create table and create index
        DB::statement("DROP TABLE IF EXISTS {$tmpTable};");
        DB::statement("CREATE TABLE {$tmpTable}({$tmpFields}) ENGINE=InnoDB;");
        DB::statement("CREATE INDEX _index_email_{$tmpTable} ON {$tmpTable}(`{$emailFieldName}`);");

        return [$tmpTable, $emailFieldName];
    }
}
