<?php
namespace App\Library\Proxy\Parser\Engines;

use App\Library\HtmlDomParser\HtmlParser;
use App\Library\Proxy\Parser\Interfaces\IProxyListParserFilter;
use App\Library\Proxy\Parser\Interfaces\IProxyParserEngines;

/**
 * Class HidemyNameParser
 */
class HidemyNameParser implements IProxyParserEngines
{

    /**
     * @var string
     */
    protected $url = 'https://hidemy.name/ru/proxy-list/';

    /**
     * @var IProxyListParserFilter
     */
    protected IProxyListParserFilter $filter;

    /**
     * @var HtmlParser
     */
    protected HtmlParser $parser;

    /**
     * @var int
     */
    protected $depth;

    /**
     * Constructor
     * @param int $depth
     */
    public function __construct($depth = 1)
    {
        $this->parser = new HtmlParser();
        $this->depth = $depth;
    }

    /**
     * Make url
     * @return string
     */
    public function url() : string
    {
        return $this->url . ( ! $this->filter->isEmpty() ? '?' . (string) $this->filter : '' ) . '#list';
    }

    /**
     * Parse proxy list
     * @param int $depth
     * @return array
     */
    public function parse(int $depth = 1) : array
    {
        $this->parser->setUrl($this->url());

        $proxies = [];

        foreach ($this->parser->dom()->find('.services_proxylist .table_block table tbody tr') as $cnode) {
            $proxy = [
                'ip'            => trim($cnode->find('td')->eq(0)->text()),
                'port'          => trim($cnode->find('td')->eq(1)->text()),
                'country'       => trim($cnode->find('td .country')->eq(0)->text()),
                'speed'         => (int) trim($cnode->find('td .bar')->eq(0)->text()),
                'type'          => trim($cnode->find('td')->eq(4)->text()),
                'anon'          => trim($cnode->find('td')->eq(5)->text()),
                'last_update'   => trim($cnode->find('td')->eq(6)->text()),
            ];
            if (method_exists(get_class($this->filter), 'getCCV')) {
                $proxy['ccv'] = trim($this->filter->getCCV($cnode->find('td .country')->eq(0)->text()));
            } else {
                $proxy['ccv'] = '';
            }

            $proxies[] = [
                'success' => true,
                'proxy' => $proxy,
            ];
        }
        return $proxies;
    }

    /**
     * @param IProxyListParserFilter $filter
     * @return void
     */
    public function setFilters(IProxyListParserFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return IProxyListParserFilter
     */
    public function getFilter() : IProxyListParserFilter
    {
        return $this->filter;
    }

    /**
     * @param int $depth
     * @return mixed
     */
    public function setDepth(int $depth = 1)
    {
        $this->depth = $depth;
    }

    /**
     * @return int
     */
    public function getDepth() : int
    {
        return $this->depth;
    }
}
