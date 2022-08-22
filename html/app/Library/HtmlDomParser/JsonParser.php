<?php
namespace App\Library\HtmlDomParser;

use App\Library\HtmlDomParser\Options\JsonParserOptions;
use App\Library\HtmlDomParser\Contracts\AbstractParser;

class JsonParser extends AbstractParser
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $json;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var bool
     */
    protected $use_cache;

    /**
     * @var string
     */
    protected $cache_group;

    /**
     * @var \DateTimeInterface|\DateInterval|int|null
     */
    protected $cache_ttl;

    /**
     * @var bool
     */
    protected $as_array;

    /**
     * JsonParser constructor.
     * @param string $url
     * @param JsonParserOptions|null $options
     */
    public function __construct(string $url = '', ?JsonParserOptions $options = null)
    {
        $this->use_cache    = !is_null($options) && $options->use_cache ?: true;
        $this->cache_group  = !is_null($options) && $options->cache_group ?: 'parser.json.';
        $this->cache_ttl    = !is_null($options) && $options->cache_ttl ?: 3600 * 24;
        $this->as_array     = !is_null($options) && $options->as_array ?: true;

        parent::__construct($url, $options);
    }

    /**
     * @param string $url
     * @param bool $set_html
     * @return string
     */
    protected function extractDocument(string $url = '', bool $set_html = true) : string
    {
        /**
         * If url is empty set new url from function parameter
         */
        if (!empty($url)) {
            $this->setUrl($url);
        }

        /**
         * Request document and get $response
         */
        $response = $this->get($this->url());

        /**
         * Set result data
         */
        if ($set_html) {
            $this->setJson($response);
        }

        return $this->data;
    }

    /**
     * @return Document
     */
    public function json() : string
    {
        return $this->json;
    }

    /**
     * @param string $json
     * @return void
     */
    public function setJson(string $json): void
    {
        $this->json = $json;
        $this->data = json_decode($this->json, $this->as_array);
    }

    /**
     * @return array
     */
    public function data() : array
    {
        return $this->data;
    }

    /**
     * @param $node
     * @param $selector
     */
    protected function saveLog($node, string $selector) : void
    {
        //Log::warning(sprintf("Element not found: %s", serialize(['url' => $this->url, 'selector' => $selector, 'node' => $node])));
    }



}
