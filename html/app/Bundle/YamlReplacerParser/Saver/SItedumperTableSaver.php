<?php
namespace App\Bundle\YamlReplacerParser\Saver;

use App\Bundle\Database\DBConnection;
use App\Bundle\YamlReplacerParser\Interfaces\ISaverInterface;
use function Respect\Stringifier\stringify;

/**
 * Class WordpressTableSaver
 */
class SitedumperTableSaver implements ISaverInterface
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
        $content = '';
        $url = '';
        $title = '';
        $type = '';
        $thumbnail = '';

        foreach ($inserts as $insert) {
            if ($insert['type'] == 'title') {
                $title = $insert['content'];
            } elseif ($insert['type'] == 'content') {
                $content = $insert['content'];
                $url = $insert['url'];
            }
        }
        if ($content) {
            $data = [
                'parent_id' => null,
                'hash' => md5($url),
                'url' => $url,
                'type' => $type,
                'content' => $content,
                'title' => $title,
                'created_at' => date("Y-m-d H:i:s"),
            ];
            dd($data);
            $this->db->insert('sitedumper_content', $data);
        }
    }

}