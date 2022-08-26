<?php
namespace App\Library\Synonimizer\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class GetTextContentFilter
 */
class GetTextContentFilter extends AbstractContentFilter implements IContentFilter
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
        return trim(
            preg_replace(['/\s+\,/', '/\s+\./', '/\s+\:/', '/\s+\;/', '/\s+\!/', '/\s+\?/'], [', ','. ',': ','; ','! ','? '],
                preg_replace('/\s+/', ' ',
                    preg_replace('/<[^<]+?>/', ' ', $text)
                )
            )
        );
    }

}
