<?php
namespace App\Bundle\YamlReplacerParser;

use App\Bundle\Database\DBConnection;
use App\Bundle\YamlReplacerParser\Filters\GetTextContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrSpecialContentFilter;
use App\Bundle\YamlReplacerParser\Filters\RemoveScriptContentFilter;
use App\Bundle\YamlReplacerParser\Filters\SynonimizerContentFilter;
use App\Bundle\YamlReplacerParser\Filters\TrimContentFilter;
use App\Bundle\YamlReplacerParser\Interfaces\ISaverInterface;
use App\Bundle\YamlReplacerParser\Saver\SitedumperTableSaver;
use App\Bundle\YamlReplacerParser\Saver\UrlSaver;
use App\Bundle\YamlReplacerParser\Saver\WordpressTableSaver;
use DOMWrap\Document;
use App\Library\HtmlDomParser\HtmlParser;
use Symfony\Component\Finder\Finder;

class ContentParser
{

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
    protected function create_db()
    {
        $this->db = new DBConnection("mysql", 'user1', '1234', 'er2night_db');
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
    protected function create_filtrator()
    {
        $this->filtrator = new ContentFiltrator();
        $this->filtrator->setFilter(new MixerBrSpecialContentFilter());
        $this->filtrator->setFilter(new MixerBrContentFilter());
        $this->filtrator->setFilter(new TrimContentFilter());
        $this->filtrator->setFilter(new GetTextContentFilter());
        $this->filtrator->setFilter(new SynonimizerContentFilter());
        $this->filtrator->setFilter(new RemoveScriptContentFilter());
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
     * @return void
     */
    public function parse(string $filepath)
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
//                        dump($rpl);

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
                                $inserts[$table][] = $insertableData;
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
            $this->saveUrl($filepath);
            $this->save($inserts);
        }
//        dump($data);
//        dump($inserts);

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