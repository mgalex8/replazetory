<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Bundle\Database\DBConnection;
use App\Library\HtmlDomParser\HtmlParser;
use App\Bundle\YamlReplacerParser\ContentFiltrator;
use App\Bundle\YamlReplacerParser\Filters\GetTextContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrSpecialContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrContentFilter;
use App\Bundle\YamlReplacerParser\Filters\TrimContentFilter;
use App\Bundle\YamlReplacerParser\YamlReplacerSchemaValidation;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

/**
 *  the name of the command is what users type after "php bin/console"
 */
class ParseIntoDbCommand extends Command
{

    protected $finder;

    protected $db;

    protected $configuration;

    protected $log_file;

    protected $files = [];

    protected $filtrator;

    protected $replace_path = '/_HTML';

    /**
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->create_log_file();
        $this->create_db();
        $this->load_configuration();
        ini_set('memory_limit', '1024M');
        $this->create_filtrator();
        $this->create_finder();
        parent::__construct($name);
    }

    /**
     * In this method setup command, description, and its parameters
     */
    protected function configure()
    {
        $this->setName('app:parse_into_db');
        $this->setDescription('Hashes provided password with BCRYPT and prints to output.');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password to be hashed.');
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
     * @return void
     */
    protected function create_db()
    {
        $this->db = new DBConnection("mysql", 'user1', '1234', 'er2night_db');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception|\Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = $input->getArgument('password');
        $hashPassword = md5($password);

        $output->writeln(sprintf(
            'Your hashed password is: %s', $hashPassword
        ));

        $this->search_files(ABS_PATH.'/', 'html');

        // creates a new progress bar (50 units)
        $countFiles = count($this->files);
        $progressBar = new ProgressBar($output, $countFiles);
        $progressBar->start();

        $i = 0;
        while ($i++ < $countFiles) {
            try {
                $this->parse($this->files[$i]);
            } catch(\Exception|\Throwable $e) {
                throw $e;
            } finally {
                $progressBar->advance();
            }
        }
        $progressBar->finish();

        $output->write(PHP_EOL);

        return Command::SUCCESS;
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
        $rows = $this->db->select('sitedumper_content', 'id', ['hash' => $hash]);
        $content_id = is_array($rows) && count($rows) > 0 ? end($rows)['id'] : null;

        if (! $content_id) {
            $htmlParser = new HtmlParser();
            $htmlParser->extractDocument($filepath);
            if (empty(trim($htmlParser->getHtml()))) {
                throw new \Exception(sprintf('File %s not open', $filepath));
            }
            $xp = new \DOMXPath($htmlParser->dom());
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
        }
        dump($data);
        dd($inserts);

        foreach ($inserts as $table => $insertableData) {
            foreach ($insertableData as $ins) {
                $this->db->insert($table, $ins);
            }
        }

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
     * @param string $str
     * @return mixed
     */
    protected function hash(string $str)
    {
        return hash('sha256', $str);
    }

    /**
     * @param string $dir
     * @param string $file_to_search
     * @return void
     */
    protected function search_files(string $dir, string $search_extension = 'html')
    {
        $files = scandir($dir);
        foreach($files as $key => $filename){

            $path = realpath($dir.DIRECTORY_SEPARATOR.$filename);
            if(!is_dir($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if(strtolower($ext) == strtolower($search_extension)) {
                    $this->files[] =  $path;
                }
            } else if($filename != "." && $filename != "..") {
                $this->search_files($path, $search_extension);
            }
        }
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