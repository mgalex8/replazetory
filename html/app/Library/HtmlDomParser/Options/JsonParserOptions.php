<?php
namespace App\Library\HtmlDomParser\Options;

use App\Library\HtmlDomParser\Contracts\AbstractOptions;

class JsonParserOptions extends AbstractOptions
{

    /**
     * @var bool
     */
    public bool $use_cache;

    /**
     * @return string
     */
    public string $cache_group;

    /**
     * @var int
     */
    public int $cache_ttl;

    /**
     * @var bool
     */
    public bool $as_array;

}
