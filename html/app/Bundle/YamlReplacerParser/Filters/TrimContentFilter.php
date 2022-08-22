<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class TrimContentFilter
 */
class TrimContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'trim';

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        return $options ? trim($text, $options) : trim($text);
    }

}
