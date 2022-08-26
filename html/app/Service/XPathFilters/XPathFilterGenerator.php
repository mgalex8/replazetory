<?php
namespace App\Service\XPathFilters;

use App\Bundle\YamlReplacerParser\ContentFiltrator;
use App\Bundle\YamlReplacerParser\Filters\GetTextContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrSpecialContentFilter;
use App\Bundle\YamlReplacerParser\Filters\TrimContentFilter;

/**
 * Class XPathFilterGenerator
 */
class XPathFilterGenerator
{
    /**
     * @var ContentFiltrator
     */
    protected $filtrator;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->filtrator = new ContentFiltrator();
        $this->filtrator->setFilter(new MixerBrSpecialContentFilter());
        $this->filtrator->setFilter(new MixerBrContentFilter());
        $this->filtrator->setFilter(new TrimContentFilter());
        $this->filtrator->setFilter(new GetTextContentFilter());
    }

    /**
     * Get Xpath Filters
     * @return array
     */
    public function getFilters() : array
    {
        return $this->filtrator->getFilters();
    }

}