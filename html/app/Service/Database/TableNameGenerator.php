<?php
namespace App\Service\Database;

use App\Bundle\Database\DBConnection;

/**
 * Class TableNameGenerator
 */
class TableNameGenerator
{

    /**
     * @var DBConnection
     */
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = new DBConnection("mysql", 'user1', '1234', 'er2night_db');
    }

    /**
     * Get tables with fields
     * @return array
     */
    public function getTables() : array
    {
        $tables = [];

        $dbTables = [
            'sitedumper_unusable_urls',
            'sitedumper_content',
            'sitedumper_additional_fields',
            'sitedumper_options',
        ];

        foreach ($dbTables as $table) {
            $table_fields = $this->db->select('information_schema.columns', '*', ['table_schema' => $this->db->getDatabaseName(), 'table_name' => $table ]);
            if ($table_fields) {
                $fields = [];
                foreach ($table_fields as $field) {
                    if (!empty($field['COLUMN_NAME'])) {
                        $fields[] = $field['COLUMN_NAME'];
                    }
                }
                $tables[$table] = $fields;
            }
        }

        return $tables;
    }
}