<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;
use App\Library\Synonimizer\Filters\ExtecranizeTagsContentFilter;
use App\Library\Synonimizer\Filters\GetTextContentFilter;
use App\Library\Synonimizer\Synonimizer;

/**
 * Class GetTextContentFilter
 */
class SynonimizerContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'synonimizer';

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        $synonimizer = new Synonimizer();
        $synonimizer->setFilter(new GetTextContentFilter());
        $synonimizer->setFilter(new ExtecranizeTagsContentFilter());

        return $synonimizer->synonimize($text);
    }

}
