<?php

namespace App\Library\RegRu;

use App\Library\RegRu\Filters\RegRuDomainsFilter;

class RegRu
{

    /**
     * @var string
     */
    protected static $csv_file_url = 'https://www.reg.ru/static_files/rereg_list.csv';

    /**
     * @var \App\Library\RegRu\Filter\RegRuDomainsFilter
     */
    protected ?RegRuDomainsFilter $filter = null;

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * Constructor
     *
     * @param RegRuDomainsFilter $filter
     */
    public function __construct(?RegRuDomainsFilter $filter = null)
    {
        if (is_null($filter)) {
            $filter = new RegRuDomainsFilter();
        }
        $this->setFilter($filter);
        $this->data = [];
    }

    /**
     * @return void
     */
    public function download_from_csv($count = null, $random = false)
    {
        $csv = $this->get_file();
        $this->data = [];
        $result = [];

        $i = 0;
        foreach ($csv as $line) {
            if ($i++ == 0) {
                continue;
            }
            $info = str_getcsv($line, ';');
            $check = $this->filter()->check_filter([
                'domain' => $info[0],
                'price' => (int) $info[1] / 1000,
                'free' => $info[3],
                'registrar' => $info[4],
            ]);
            if ($check) {
                $this->data[] = $info;
                if (!$random && !is_null($count) && $i > $count) break;
                if ( $random && !is_null($count) && $i > $count * 2) break;
            }
        }

        /**
         * If parameter $random == true then randomize elements
         */
        if ($random) {
            $keys = array_rand($this->data, $count);
            foreach ($keys as $key) {
                $result[] = $this->data[ $key ];
            }
        } else {
            $result = $this->data;
        }

        return $result;
    }

    /**
     * Get csv file with domains list
     *
     * @return array|false
     * @throws \Exception
     */
    protected function get_file()
    {
        $cache_file = $this->get_file_from_cache();
        if ($cache_file !== null) {
            return $cache_file;
        } else {
            $this->clear_cache_files();

            $content = file_get_contents(self::$csv_file_url);
            $this->save_cache_file($content);

            return file($cache_file['fullpath']);
        }
    }

    /**
     * Get file from cache; directory name
     *      $cache_file_path['path']
     * filename
     *      $cache_file_path['filename']
     *
     * @return array|false|null
     */
    protected function get_file_from_cache()
    {
        $cache_file_path = $this->get_cache_file_path();
        if (! is_null($cache_file_path) && file_exists($cache_file_path['fullpath'])) {
            return file($cache_file_path['fullpath']);
        } else {
            return null;
        }
    }

    /**
     * Save cache file into path; directory name
     *      $cache_file_path['path']
     * filename
     *      $cache_file_path['filename']
     *
     * @param string $content
     * @return void
     * @throws \Exception
     */
    protected function save_cache_file(string $content)
    {
        $cache_file_path = $this->get_cache_file_path();
        if (! $this->create_cache_directory($cache_file_path['path'])) {
            throw new \Exception(sprintf('Unable to open cache directory %s', $cache_file_path['path']));
        }
        $file = fopen($cache_file_path['fullpath'], 'w+');
        if (! $file) {
            throw new \Exception(sprintf('Unable to open cache file %s', $cache_file_path['fullpath']));
        }

        fwrite($file, $content);
        fclose($file);
    }

    /**
     * Clear all files from directory path
     *      $cache_file_path['path'],
     * but ignores current date file
     *      $cache_file_path['filename']
     *
     * @return void
     */
    protected function clear_cache_files()
    {
        $cache_file_path = $this->get_cache_file_path();
        foreach( glob($cache_file_path['path'] . DIRECTORY_SEPARATOR . "*") as $file ) {
            if ( basename($file) != $cache_file_path['filename'] ){
                unlink($file);
            }
        }
    }

    /**
     * @return string[]|null
     */
    protected function get_cache_file_path()
    {
        $upload_dir = wp_upload_dir();
        if (!empty( $upload_dir['basedir'])) {
            $path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'regru';
            $filename = 'rereg_list_' . date('Y_m_d') . '.csv';
            return ['path' => $path, 'filename' => $filename, 'fullpath' => $path . DIRECTORY_SEPARATOR . $filename];
        }
        else {
            return null;
        }
    }

    /**
     * Create cache directory
     *
     * @param string $path
     * @return bool
     */
    public function create_cache_directory(string $path)
    {
        if (! file_exists($path)) {
            mkdir($path, 755, true);
        }
        return file_exists($path);
    }

    /**
     * Set filter
     *
     * @param \App\Library\RegRu\Filter\RegRuDomainsFilter $filter
     * @return void
     */
    public function setFilter(RegRuDomainsFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Get filter
     *
     * @return \App\Library\RegRu\Filter\RegRuDomainsFilter
     */
    public function filter() : RegRuDomainsFilter
    {
        return $this->filter;
    }

    /**
     * Get filtered data
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

}
