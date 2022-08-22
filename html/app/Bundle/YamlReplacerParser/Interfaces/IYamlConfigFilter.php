<?php
namespace App\Bundle\YamlReplacerParser\Interfaces;

/**
 * IYamlConfigFilter interface
 */
interface IYamlConfigFilter
{
    /**
     * @return void
     */
    public function configure() : void;

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter(string $text, array $options = []) : string;

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string;

}
