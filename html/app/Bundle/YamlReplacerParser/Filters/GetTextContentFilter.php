<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class GetTextContentFilter
 */
class GetTextContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'get_text';

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        return trim(preg_replace('/<[^<]+?>/', ' ', $text));
    }

}
