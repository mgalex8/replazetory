<?php
namespace App\Bundle\YamlReplacerParser\Saver;

use App\Bundle\Database\DBConnection;
use App\Bundle\YamlReplacerParser\Interfaces\ISaverInterface;

/**
 * Class WordpressTableSaver
 */
class WordpressTableSaver implements ISaverInterface
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
        if (isset($inserts['sitedumper_content'])) {
            $this->save_sitedumper_content($inserts['sitedumper_content']);
        } elseif (isset($inserts['sitedumper_additional_fields'])) {
            $this->save_sitedumper_additional_filrds($inserts['sitedumper_additional_fields']);
        }
    }

    /**
     * @param array $inserts
     * @return void
     */
    public function save_sitedumper_content(array $inserts) : void
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
            }
        }
        if ($content) {
            $data = [
                'post_author' => 1,
                'post_date' => date('Y-m-d H:i:s'),
                'post_date_gmt' => date('Y-m-d H:i:s'),
                'post_content' => $content,
                'post_title' => $title,
                'post_excerpt' => '',
                'post_status' => 'publish',
                'comment_status' => 'open',
                'ping_status' => 'open',
                'post_password' => '',
                'post_name' => \URLify::slug($title),
                'to_ping' => '',
                'pinged' => '',
                'post_modified' => date('Y-m-d H:i:s'),
                'post_modified_gmt' => date('Y-m-d H:i:s'),
                'post_content_filtered' => '',
                'post_parent' => 0,
                'guid' => '/'.\URLify::slug($title).'/',
                'menu_order' => 0,
                'post_type' => 'post',
                'post_mime_type' => '',
                'comment_count' => 0,
            ];
            $this->db->insert('wp_posts', $data);
        }
    }

    /**
     * @param array $inserts
     * @return void
     */
    public function save_sitedumper_additional_filrds(array $inserts) : void
    {
        //
    }

}