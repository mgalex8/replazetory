<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class TrimContentFilter
 */
class ClearfxTagImageContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'clearfix_image';

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        return preg_replace('/<img\s+[^>]*src\s*=\s*"([^"]+)"[^>]*>/', '<img src="\1">', $text);;
    }

}
