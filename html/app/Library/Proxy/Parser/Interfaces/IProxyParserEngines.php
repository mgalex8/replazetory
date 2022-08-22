<?php

namespace App\Library\Proxy\Parser\Interfaces;

interface IProxyParserEngines
{

    /**
     * @param IProxyListParserFilter $filter
     * @return void
     */
    public function setFilters(IProxyListParserFilter $filter);

    /**
     * @return IProxyListParserFilter
     */
    public function getFilter() : IProxyListParserFilter;

    /**
     * Make url
     * @return string
     */
    public function url();

    /**
     * @return void
     */
    /**
     * @return array
     */
    public function parse() : array;

}
