<?php
namespace App\Bundle\YamlReplacerParser\Saver;

use App\Bundle\Database\DBConnection;
use App\Bundle\YamlReplacerParser\Interfaces\ISaverInterface;

/**
 * Class WordpressTableSaver
 */
class UrlSaver implements ISaverInterface
{

    protected $db;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->db = new DBConnection("mysql", 'user1', '1234', 'er2night_db');
    }

    /**
     * @param array $inserts
     * @return void
     */
    public function saveToDatabase(array $inserts) : void
    {
        $this->db->insert('sitedumper_urls', [
            'url' => $inserts['url'],
            'hash' => $inserts['hash'],
            'type' => $inserts['type'] ?: 'page',
        ]);
    }

}