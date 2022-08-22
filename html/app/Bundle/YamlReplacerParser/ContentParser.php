<?php
namespace App\Bundle\YamlReplacerParser;

use DOMWrap\Document;
use App\Library\HtmlDomParser\HtmlParser;

class ContentParser
{

    /**
     * @var HtmlParser
     */
    protected $parser;

    /**
     * @var Document
     */
    protected $document;

    /**
     * @param string $filepath
     */
    public function __construct(string $filepath)
    {
        $this->parser = new HtmlParser();
    }

    /**
     * @param string $filepath
     * @return void
     */
    public function createDocument(string $filepath) : Document
    {
        $this->parser->extractDocument($filepath);
        $this->setDocument($this->parser->dom());
    }

    /**
     * @return Document|null
     */
    public function getDocument() : ?Document
    {
        return $this->document;
    }

    /**
     * @param Document $document
     * @return void
     */
    public function setDocument(Document $document) : void
    {
        $this->document = $document;
    }

    /**
     * @param string $filepath
     * @return void
     */
    public function parse(string $filepath)
    {
        $data = [];
        $url = $this->create_url_from_filepath($filepath);
        $hash = $this->hash($url);
        $rows = $this->db->select('sitedumper_content', 'id', ['hash' => $hash]);
        $content_id = is_array($rows) && count($rows) > 0 ? end($rows)['id'] : null;

        if (! $content_id) {
            $this->createDocument($filepath);
            $xp = new \DOMXPath($this->getDocument());
            foreach ($this->configuration as $key => $config) {
                $processing = !isset($config['processing']) || $config['processing'] == 'true' ? true : false;
                $current_directory = ! isset($config['directory']) || (isset($config['directory']) && ($config['directory'] === '*') || preg_match($config['directory'], $url)) ? true : false;
                if ($processing && $current_directory) {
                    $matches_array = $config['matches'];
                    foreach ($matches_array as $matches) {
                        $item_replacers = [];
                        $item = [];
                        $item['key'] = $key;
                        $item['matches'] = $matches;
                        $item['matches']['xpath_found'] = 0;
                        $content_original = '';
                        $content_replacer = '';
                        foreach ($xp->query($matches['xpath']) as $node) {
                            if ($node instanceof \DOMAttr) {
                                $content_original = $node->textContent;
                            } else {
                                $content_original = (string) $node;
                            }
                            $rpl = [ 'node' => $node->textContent ];
                            $item['matches']['xpath_found'] = 1;

                            /** apply filters **/
                            $content_replacer = $content_original;
                            if (! empty($item['matches']['filters'])) {
                                $content_replacer = $this->filterContent($content_original, $item['matches']['filters']);
                                $rpl['from'] = $content_original;
                                $rpl['to'] = $content_replacer;
                                $rpl['filters'] = array_keys($item['matches']['filters']);
                                $rpl['filtered'] = 1;
                            }

                            /** apply replacers **/
                            if (isset($matches['replacers'])) {
                                foreach ($matches['replacers'] as $replace) {
                                    if ($item['matches']['xpath'] == '//title') {
                                        dump($replace);
                                    }
                                    $found_replacer = preg_match($replace['from'], $content_replacer);
                                    if ($found_replacer) {
                                        $content_replacer = preg_replace($replace['from'], $replace['to'], $content_replacer);
                                        $rpl['tpl_from'] = $replace['from'];
                                        $rpl['tpl_to'] = $replace['to'];
                                        $rpl['content_from'] = $content_original;
                                        $rpl['content_to'] = $content_replacer;
                                        $rpl['replaced'] = 1;
                                        $item_replacers[] = $rpl;
                                    } else {
                                        $rpl['tpl_from'] = $replace['from'];
                                        $rpl['tpl_to'] = $replace['to'];
                                        $rpl['content_from'] = '';
                                        $rpl['content_to'] = '';
                                        $rpl['replaced'] = 0;
                                        $item_replacers[] = $rpl;
                                    }
                                }
                            }
                            if (isset($rpl['filtered']) && $rpl['filtered'] == 1) {
                                $item_replacers[] = $rpl;
                            }
                        }

                        /** save to database **/
                        if (isset($matches['save'])) {
                            //savw
                            $table = str_contains($matches['save']['table'], 'sitedumper_') ? $matches['save']['table'] : 'sitedumper_'.$matches['save']['table'];
                            if ($table === 'sitedumper_content') {
                                $parent_id = null;
                                if (isset($matches['save']['parent_id'])) {
                                    $rows = $this->db->select($table, 'id', ['hash' => $hash]);
                                    if (count($rows) == 1) {
                                        $parent_id = $rows[0]['id'];
                                    } elseif (count($rows) > 1) {
                                        $parent_id = $rows[count($rows) - 1]['id'];
                                    }
                                }
                                $insertableData = [
                                    'type' => $matches['save']['type'],
                                    'parent_id' => $parent_id,
                                    'hash' => $parent_id == null ? $hash : null,
                                    'url' => $parent_id === null ? $url : null,
                                    'content' => $content_replacer,
//                                    'title' => $data['title'] ?: null,
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
//                                $this->doInsert($table, $insertableData);
                                $item['inserts'][$table][] = $insertableData;
                            }
                            elseif ($table === 'sitedumper_additional_fields') {
                                $insertableData = [
                                    'name' => $matches['save']['name'],
                                    'value' => $content_replacer,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => null,
                                ];
//                                $this->doInsert($table, $insertableData);
                                $item['inserts'][$table][] = $insertableData;
                            }
                        }
                        // add data replacers
                        if ($item_replacers) {
                            $item['replacers'] = $item_replacers;
                        }

                        $data[] = $item;
                    }

                }
            }
        }
        dump($data);
        return;
    }

    /**
     * @param string $content
     * @param array $filters
     * @return void
     */
    protected function filterContent(string $content, array $filters = [])
    {
        $result = $content;
        foreach ($filters as $filter_name => $filter_options) {
            $result = $this->filtrator->filter($result, $filter_name, $filter_options);
        }
        return $result;
    }

    /**
     * Save data to table 'sitedumper_unusable_urls'
     * @param $filepath
     * @param $content
     * @return void
     */
    protected function save_unusable_urls_to_db(string $url, array $data)
    {
        $hash = $this->hash($url);
        $rows = $this->db->select('sitedumper_unusable_urls', 'id', ['hash' => $hash]);
        return is_array($rows) && count($rows) > 0 ? end($rows)['id'] : $this->db->insert('sitedumper_unusable_urls', [
            'hash' => $hash,
            'url' => $url,
        ]);
    }

    /**
     * Save data to table 'sitedumper_content'
     * @param $filepath
     * @param $content
     * @return void
     */
    protected function save_content_to_db(string $filepath, array $data)
    {
        return $this->db->insert('sitedumper_content', [
            'parent_id' => $data['parent_id'] ?: null,
            'hash' => $data['hash'] ?: null,
            'url' => $data['url'] ?: null,
            'type' => $data['type'] ?: 'unknown',
            'content' => $data['content'] ?: null,
            'title' => $data['title'] ?: null,
            'created_at' => $data['created_at'] ?: date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Save data to table 'sitedumper_content'
     * @param $filepath
     * @param $content
     * @return void
     */
    protected function save_additional_fields_to_db(string $filepath, array $data)
    {
        return $this->db->insert('sitedumper_content', [
            'name' => $data['parent_id'] ?: null,
            'value' => $data['hash'] ?: null,
            'created_at' => $data['created_at'] ?: date('Y-m-d H:i:s'),
            'updated_at' => $data['created_at'] ?: date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @param string $filepath
     * @return array|string|string[]
     */
    protected function create_url_from_filepath(string $filepath)
    {
        return str_replace(ABS_PATH, '', $filepath);
    }

    /**
     * @param string $str
     * @return mixed
     */
    protected function hash(string $str)
    {
        return hash('sha256', $str);
    }


}