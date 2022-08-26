<?php
namespace App\Library\Synonimizer\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class GetTextContentFilter
 */
class ExtecranizeTagsContentFilter extends AbstractContentFilter implements IContentFilter
{
    /**
     * @var string
     */
    protected $name = 'extecranize_tags';

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        return str_replace(['&lt;', '&gt;', '&quot;'], ['<','>','"'], $text);
    }

}
