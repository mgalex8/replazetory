<?php
namespace App\Bundle\YamlReplacerParser;

use App\Bundle\Database\DBConnection;
use App\Bundle\YamlReplacerParser\Filters\GetTextContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrSpecialContentFilter;
use App\Bundle\YamlReplacerParser\Filters\TrimContentFilter;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlReplacerSchemaValidation
 */
class YamlReplacerSchemaValidation
{

    /**
     * @var string
     */
    protected $yaml_file_path;

    /**
     * @var array|null
     */
    protected $data;

    /**
     * @var DBConnection
     */
    protected $db;

    /**
     * @var ContentFiltrator
     */
    protected $filtrator;

    /**
     * @param string $yaml_file_path
     */
    public function __construct(string $yaml_file_path)
    {
        $this->yaml_file_path = $yaml_file_path;
        $this->db = new DBConnection("mysql", 'user1', '1234', 'er2night_db');

        $this->filtrator = new ContentFiltrator();
        $this->filtrator->setFilter(new MixerBrSpecialContentFilter());
        $this->filtrator->setFilter(new MixerBrContentFilter());
        $this->filtrator->setFilter(new TrimContentFilter());
        $this->filtrator->setFilter(new GetTextContentFilter());
    }

    /**
     * @param string $yaml_file_path
     * @return mixed
     * @throws \Exception
     */
    public function load(string $yaml_file_path = '')
    {
        $yaml_file_path = $yaml_file_path ?: $this->yaml_file_path;
        if (! file_exists($yaml_file_path)) {
            throw new \Exception(sprintf('Configuration file not found: %s', $yaml_file_path));
        }
        try {
            $yaml = file_get_contents($yaml_file_path);
            $this->data = Yaml::parse($yaml);
            return $this->data;
        } catch (ParseException $exception) {
            throw new $exception(sprintf('Unable to parse the YAML string: %s', $exception->getMessage()));
        }
        return null;
    }

    /**
     * @return void
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function validate(string $yaml_file_path = '')
    {
        $yaml_file_path = $yaml_file_path ?: $this->yaml_file_path;

        $this->data = $this->load($yaml_file_path);
        if (empty($this->data)) {
            throw new ParseException(sprintf('File config is empty %s', $yaml_file_path));
        }

        $all_content_filters = $this->filtrator->getFilters();

        $cnf = [];
        foreach ($this->data as $key => $config) {
            $config['processing'] = !isset($config['processing']) || (is_string($config['processing']) && $config['processing'] == 'true') || (is_bool($config['processing']) && $config['processing']) ? 'true' : 'false';

            if (! isset($config['matches']) || !is_array($config['matches'])) {
                throw new ParseException(sprintf('Do not set required parameter `matches` on %s', $key));
            } else {
                $matches_array = $config['matches'];
                $matchesindex = 0;
                $matches_pl = [];
                foreach ($matches_array as $matches) {
                    $matches_pl[$matchesindex] = $matches;
                    if (! isset($matches['xpath'])) {
                        throw new ParseException(sprintf('Do not set required parameter `xpath` on %1$s.matches.%2$d, please setup %1$s.matches.%2$d.xpath parameter on yml config', $key, $matchesindex));
                    }
                    if (isset($matches['replacers'])) {
                        if (is_array($matches['replacers']) && array_key_exists(0, $matches['replacers'])) {
                            $replindex = 0;
                            foreach ($matches['replacers'] as $repl) {
                                if (! isset($repl['from'])) {
                                    throw new ParseException(sprintf('Do not set required parameter `from` on %1$s.matches.%2$d.replacers.%3$d, please setup %1$s.matches.%2$d.replacers.%3$d.from parameter on yml config', $key, $matchesindex, $replindex));
                                }
                                if (! isset($repl['to'])) {
                                    throw new ParseException(sprintf('Do not set required parameter `from` on %1$s.matches.%2$d.replacers.%3$d, please setup %1$s.matches.%2$d.replacers.%3$d.to parameter on yml config', $key, $matchesindex, $replindex));
                                }
                                $replindex++;
                            }
                            $replacers = $matches['replacers'];
                        }
                        else {
                            if (! isset($matches['replacers']['from'])) {
                                throw new ParseException(sprintf('Do not set required parameter `to` on %1$s.matches.%2$d.replacers, please setup %1$s.matches.%2$d.replacers.from parameter on yml config', $key, $matchesindex));
                            }
                            if (! isset($matches['replacers']['to'])) {
                                throw new ParseException(sprintf('Do not set required parameter `from` on %1$s.matches.%2$d.save, please setup %1$s.matches.%2$d.replacers.to parameter on yml config', $key, $matchesindex));
                            }
                            $replacers = [ [ 'from' => $matches['replacers']['from'], 'to' => $matches['replacers']['to'] ] ];
                        }
                        $matches_pl[$matchesindex]['replacers'] = $replacers;
                    }

                    if (isset($matches['filters'])) {
                        $filters = [];
                        if (! is_array($matches['filters'])) {
                            throw new ParseException(sprintf('Parameter `filters` on %1$s.matches.%2$d.filters must be type of array, %3$s', $key, $matchesindex, gettype($matches['filters']) == 'object' ? get_class($matches['filters']) : gettype($matches['filters'])));
                        }
                        $filterindex = 0;
                        foreach ($matches['filters'] as $filter_parameters) {
                            if (is_string($filter_parameters)) {
                                $filter_name = $filter_parameters;
                                if (!array_key_exists($filter_name, $all_content_filters)) {
                                    throw new ParseException(sprintf('Filter `%3$s` not found in class %4$s, check filter names on %1$s.matches.%2$d.filters', $key, $matchesindex, $filter_name, get_class($this)));
                                }
                                $filters[$filter_name] = [];
                            } elseif(is_array($filter_parameters)) {
                                foreach ($filter_parameters as $f_n => $f_opt) {
                                    $filter_name = $f_n;
                                    $filter_parameters = $f_opt;
                                    if (!array_key_exists($filter_name, $all_content_filters)) {
                                        throw new ParseException(sprintf('Filter `%3$s` not found in class %4$s, check filter names on %1$s.matches.%2$d.filters', $key, $matchesindex, $filter_name, get_class($this)));
                                    }
                                    $filters[$filter_name] = $filter_parameters;
                                }
                            } else {
                                throw new ParseException(sprintf('Filter number `%3$s`on %1$s.matches.%2$d.filters.%3$s must be string or array', $key, $matchesindex, $filterindex, get_class($this)));
                            }
                            $filterindex++;
                        }
                        $matches_pl[$matchesindex]['filters'] = $filters;
                    }

                    if (isset($matches['save'])) {
                        if (! isset($matches['save']['table'])) {
                            throw new ParseException(sprintf('Do not set required parameter `table` on %1$s.matches.%2$d.save, please setup $1&s.matches.%2$d.save.table parameter on yml config', $key, $matchesindex));
                        } else {
                            $table = str_contains($matches['save']['table'], 'sitedumper_') ? $matches['save']['table'] : 'sitedumper_'.$matches['save']['table'];
                            $matches_pl[$matchesindex]['save']['table'] = $table;
                            $table_fields = $this->db->select('information_schema.columns', '*', ['table_schema' => $this->db->getDatabaseName(), 'table_name' => $table ]);
                            if ($table_fields) {
                                $field_names = [];
                                foreach ($table_fields as $field) {
                                    $field_names[] = $field['COLUMN_NAME'];
                                }
                                foreach ($matches['save'] as $param => $value) {
                                    if ($param !== 'table' && ! in_array($param, $field_names)) {
                                        throw new ParseException(sprintf('Parameter `%3$s` on %1$s.matches.%2$d.save not exists in table %4$s, please setup %1$s.matches.%2$d.save.%3$s parameter on yml config', $key, $matchesindex, $param, $table));
                                    }
                                }
                            }
                        }
                    }
                    $matchesindex++;
                }
                $config['matches'] = $matches_pl;
            }
            $cnf[$key] = $config;
        }
        $this->data = $cnf;
        return $this->data;
    }

}