<?php
namespace App\Library\Proxy\Parser\Interfaces;

interface IProxyListParserFilter
{

    /**
     * Set parameters
     * @param array $args
     * @return void
     */
    public function setParameters(array $args = []);

    /**
     * Get parameters
     * @return array
     */
    public function getParameters() : array;

    /**
     * Set pagination
     * @param int $page
     * @return void
     */
    public function setPage(int $page = 1);

    /**
     * Get page
     * @return int
     */
    public function getPage() : int;

    /**
     * Get filter url string
     * @return string
     */
    public function getUrlString() : string;

    /**
     * @return bool
     */
    public function isEmpty() : bool;

}
