<?php
namespace App\Bundle\YamlReplacerParser;

use App\Bundle\Database\DBConnection;
use App\Bundle\YamlReplacerParser\Interfaces\ISaverInterface;
use App\Bundle\YamlReplacerParser\Saver\SitedumperTableSaver;
use App\Bundle\YamlReplacerParser\Saver\UrlSaver;
use App\Bundle\YamlReplacerParser\Saver\WordpressTableSaver;
use App\Bundle\YamlReplacerParser\Traits\ContentFiltratorSetup;
use App\Bundle\YamlReplacerParser\Traits\DatabaseSetup;
use DOMWrap\Document;
use App\Library\HtmlDomParser\HtmlParser;
use Symfony\Component\Finder\Finder;

class ContentParser
{

    /**
     * use section
     */
    use ContentFiltratorSetup, DatabaseSetup;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var DBConnection
     */
    protected $db;

    /**
     * @var string
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $log_file;

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var ContentFiltrator
     */
    protected $filtrator;

    /**
     * @var ISaverInterface
     */
    protected $saver;

    /**
     * @var string
     */
    protected $replace_path = '/_HTML';

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
    public function __construct()
    {
        $this->parser = new HtmlParser();
        $this->create_log_file();
        $this->create_db();
        $this->load_configuration();
        ini_set('memory_limit', '1024M');
        $this->create_filtrator();
        $this->create_finder();
        $this->create_savers();
    }

    /**
     * @return void
     */
    protected function create_finder()
    {
        $this->finder = new Finder();
    }

    /**
     * @return void
     */
    protected function create_savers()
    {
        $this->saver = new WordpressTableSaver();
//        $this->saver = new SitedumperTableSaver();
    }

    /**
     * @return void
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    protected function load_configuration()
    {
        $yaml_file_path = ABS_PATH.'/configuration/xpath.yml';
        $yamlReplacerParserSchemaValidator = new YamlReplacerSchemaValidation($yaml_file_path);
        $this->configuration = $yamlReplacerParserSchemaValidator->validate($yaml_file_path);
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
     * @param ISaverInterface $saver
     * @return void
     */
    public function setSaver(ISaverInterface $saver)
    {
        $this->saver = $saver;
    }


    /**
     * @param string $filepath
     * @return array|string|string[]
     */
    protected function create_url_from_filepath(string $filepath)
    {
        return str_replace(ABS_PATH.$this->replace_path, '', $filepath);
    }

    /**
     * @param string $filepath
     * @param bool $save
     * @return array
     * @throws \Exception
     */
    public function parse(string $filepath, bool $save = true) : array
    {
        $data = [];
        $inserts = [];
        $url = $this->create_url_from_filepath($filepath);
        $hash = $this->hash($url);
        $rows = $this->db->select('sitedumper_urls', 'id', ['url' => $url]);
        $url_id = is_array($rows) && count($rows) > 0 ? reset($rows)['id'] : null;

        if (! $url_id) {
            $this->parser->extractDocument($filepath);
            if (empty(trim($this->parser->getHtml()))) {
                throw new \Exception(sprintf('File %s not open', $filepath));
            }
            $xp = new \DOMXPath($this->parser->dom());
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
                        $content_original_filtrated = '';
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
                                $content_replacer = $this->filterContent($content_original, $item['matches']['filters'], false);
                                $content_original_filtrated = $this->filterContent($content_original, $item['matches']['filters'], true);
                                $rpl['from'] = $content_original;
                                $rpl['to'] = $content_replacer;
                                $rpl['filters'] = array_keys($item['matches']['filters']);
                                $rpl['filtered'] = 1;
                            }

                            /** apply replacers **/
                            if (isset($matches['replacers'])) {
                                foreach ($matches['replacers'] as $replace) {
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
                            // taxonomy
                            $taxonomies = [];
                            if (isset($matches['save']['taxonomy'])) {
                                if (is_array($matches['save']['taxonomy_name']) && isset($matches['save']['taxonomy_name']['xpath'])) {
                                    foreach ($xp->query($matches['save']['taxonomy_name']['xpath']) as $taxonomy_name) {
                                        $taxonomies[] = [
                                            'taxonomy' => $matches['save']['taxonomy'],
                                            'name' => $taxonomy_name->textContent,
                                            'slug' => isset($matches['save']['taxonomy_slug']) ? $matches['save']['taxonomy_slug'] : null,
                                        ];
                                    }
                                } else {
                                    $taxonomies[] = [
                                        'taxonomy' => $matches['save']['taxonomy'],
                                        'name' => isset($matches['save']['taxonomy_name']) ? $matches['save']['taxonomy_name'] : null,
                                        'slug' => isset($matches['save']['taxonomy_slug']) ? $matches['save']['taxonomy_slug'] : null,
                                    ];
                                }
                            }

                            $max_ids = [
                                'post_id' => isset($matches['save']['max_id']) ? $matches['save']['max_id'] : null,
                                'term_id' => isset($matches['save']['max_term_id']) ? $matches['save']['max_term_id'] : null,
                                'term_taxonomy_id' => isset($matches['save']['max_term_taxonomy_id']) ? $matches['save']['max_term_taxonomy_id'] : null,
                            ];

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
                                    'save_original' => (bool) $matches['save']['save_original'],
                                    'original' => $content_original_filtrated,
                                    'title' => $data['title'] ?: null,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'taxonomies' => $taxonomies,
                                    'max_ids' => $max_ids,
                                ];
//                                $this->doInsert($table, $insertableData);
                                $item['inserts'][$table][] = $insertableData;
                                $inserts[$table][] = $insertableData;
                            }
                            elseif ($table === 'sitedumper_additional_fields') {
                                $insertableData = [
                                    'name' => $matches['save']['name'],
                                    'value' => $content_replacer,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => null,
                                    'taxonomies' => $taxonomies,
                                ];
//                                $this->doInsert($table, $insertableData);
                                $item['inserts'][$table][] = $insertableData;
                                $inserts[$table][] = $insertableData;
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
            if ($save) {
                $this->saveUrl($filepath);
                $this->save($inserts);
            }
        }

        if (! $save) {
            dump($data);
            dump($inserts);
        }

        return $data;
    }

    /**
     * @param array $inserts
     * @param ISaverInterface|null $saver
     * @return void
     */
    protected function save(array $inserts, ?ISaverInterface $saver = null)
    {
        $saver = $saver ?: $this->saver;

        $saver->saveToDatabase($inserts);
    }

    /**
     * @param array $inserts
     * @param ISaverInterface|null $saver
     * @return void
     */
    protected function saveUrl(string $filepath, ?ISaverInterface $saver = null)
    {
        $url = $this->create_url_from_filepath($filepath);
        $hash = $this->hash($url);

        $saver = new UrlSaver();

        $saver->saveToDatabase([
            'url' => $url,
            'hash' => $hash,
            'type' => 'post',
        ]);
    }

    /**
     * @param string $content
     * @param array $filters
     * @param bool $original
     * @return string|null
     * @throws \Exception
     */
    protected function filterContent(string $content, array $filters = [], bool $original = false)
    {
        $result = $content;
        foreach ($filters as $filter_name => $filter_options) {
            $fitrated = true;
            if (isset($filter_options['original'])) {
                if ($original) {
                    $fitrated = $original && $filter_options['original'];
                }
                unset($filter_options['original']);
            }

            if (isset($filter_options['many'])) {
                foreach ($filter_options['many'] as $many) {
                    $result = $this->filterOnce($result, $filter_name, $many, $fitrated);
                }
            } else {
                $result = $this->filterOnce($result, $filter_name, $filter_options, $fitrated);
            }
        }
        return $result;
    }

    /**
     * @param string $content
     * @param string $filter_name
     * @param array $options
     * @param bool $fitrated
     * @return void
     * @throws \Exception
     */
    protected function filterOnce(string $content, string $filter_name, array $options = [], bool $fitrated = false)
    {
        if ($fitrated) {
            return $this->filtrator->filter($content, $filter_name, $options);
        } else {
            return $content;
        }
    }

    /**
     * @param string $str
     * @return mixed
     */
    protected function hash(string $str)
    {
        return hash('md5', $str);
    }

    /**
     * @param string $filepath
     * @return void
     */
    protected function save_log(string $filepath)
    {
        if (! $this->exists_file_into_log($filepath)) {
            $log = fopen($filepath, "w+");
            fwrite($log, $filepath . PHP_EOL);
            fclose($log);
        }
    }

    /**
     * @param string $filepath
     * @return void
     */
    protected function exists_file_into_log(string $filepath)
    {
        $logContent = file_get_contents($this->log_file);
        return strpos($logContent, $filepath) !== false;
    }

    /**
     * @return void
     */
    protected function create_log_file()
    {
        if (! file_exists(ABS_PATH.'/log')) {
            mkdir(ABS_PATH.'/log', 0777, false);
        }
        $this->log_file = ABS_PATH.'/log/parser.log';
    }

}