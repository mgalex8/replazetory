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
     * @throws \Exception
     */
    public function save_sitedumper_content(array $inserts) : void
    {
        $content = '';
        $url = '';
        $title = '';
        $type = '';
        $thumbnail = '';

        $insertable = [];

        foreach ($inserts as $insert) {
            if ($insert['type'] == 'title') {
                $title = $insert['content'];
            } elseif ($insert['type'] == 'content') {
                $content = $insert['content'];
                $insertable = $insert;
            }
        }
        if ($content) {
            if (isset($insertable['max_ids'])) {
                if (isset($insert['max_ids']['post_id'])) {
                    $this->setAutoIncrement('wp_posts', $insertable['max_ids']['post_id']);
                }
                if (isset($insertable['max_ids']['term_id'])) {
                    $this->setAutoIncrement('wp_terms', $insertable['max_ids']['term_id']);
                }
                if (isset($insertable['max_ids']['term_taxonomy_id'])) {
                    $this->setAutoIncrement('wp_term_taxonomy', $insertable['max_ids']['term_taxonomy_id']);
                }
            }

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
            $post_id = $this->db->insert('wp_posts', $data);

            if ($insertable['save_original']) {
                $data = [
                    'post_author' => 1,
                    'post_date' => date('Y-m-d H:i:s'),
                    'post_date_gmt' => date('Y-m-d H:i:s'),
                    'post_content' => $insertable['original'],
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
                    'post_parent' => $post_id,
                    'guid' => '/'.\URLify::slug($title).'/',
                    'menu_order' => 0,
                    'post_type' => 'revision',
                    'post_mime_type' => '',
                    'comment_count' => 0,
                ];
                $post_id = $this->db->insert('wp_posts', $data);
            }

            if (isset($insertable['taxonomies']) && ! empty($insertable['taxonomies'])) {
                foreach ($insertable['taxonomies'] as $taxonomy) {
                    $this->save_taxonomy($taxonomy, $post_id);
                }
            }
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

    /**
     * @param array $inserts
     * @param int $post_id
     * @return void
     * @throws \Exception
     */
    public function save_taxonomy(array $insert, int $post_id) : void
    {
        $rows = $this->db->select('wp_terms', 'term_id', ['name' => $insert['name']]);
        if (count($rows) === 0) {
            $term_id = $this->db->insert('wp_terms', [
                'name' => $insert['name'] ?? uniqid(),
                'slug' => $insert['slug'] ?? \URLify::slug($insert['name']),
            ]);
            $term_taxonomy_id = $this->db->insert('wp_term_taxonomy', [
                'term_id' => $term_id,
                'taxonomy' => $insert['taxonomy'],
                'description' => $insert['description'] ?? '',
            ]);
            $this->db->insert('wp_term_relationships', [
                'object_id' => $post_id,
                'term_taxonomy_id' => $term_taxonomy_id,
            ]);
        }
    }

    /**
     * @param string $table
     * @param int|string $number
     * @return void
     * @throws \Exception
     */
    protected function setAutoIncrement(string $table, $number)
    {
        $number = $number + 1;
        $idName = null;

        $selectFields = $this->db->selectInformationSchemaFields($table);
        foreach ($selectFields as $field) {
            if (isset($field['COLUMN_NAME'])) {
                if ($field['COLUMN_KEY'] == 'PRI') {
                    $idName = $field['COLUMN_NAME'];
                }
            }
        }

        if ($idName) {
            $result = $this->db->dbQuery('SELECT MAX('.$idName.') AS maxdata FROM '.$table.';');
            if (mysqli_num_rows($result) > 0) {
                $fetch = mysqli_fetch_assoc($result);
                $id = $fetch['maxdata'];
            }
            if ($id > $number) {
                $this->db->dbQuery('ALTER TABLE '.$table.' AUTO_INCREMENT='.$id);
            } else {
                $this->db->dbQuery('ALTER TABLE '.$table.' AUTO_INCREMENT='.$number);
            }
        }
    }
}