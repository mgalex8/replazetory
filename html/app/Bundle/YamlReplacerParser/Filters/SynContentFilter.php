<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;
use App\Library\Synonimizer\Filters\ExtecranizeTagsContentFilter;
use App\Library\Synonimizer\Filters\GetTextContentFilter;
use App\Library\Synonimizer\Synonimizer;
use DOMWrap\Document;

/**
 * Class GetTextContentFilter
 */
class SynContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'syn';

    /**
     * @var Synonimizer
     */
    protected $synonimizer;

    /**
     * Constructor class
     */
    public function __construct()
    {
        $this->synonimizer = new Synonimizer();
        $this->synonimizer->setFilter(new GetTextContentFilter());
        $this->synonimizer->setFilter(new ExtecranizeTagsContentFilter());
        parent::__construct();
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        return $this->synonimizer->synonimize($text);
    }

}
