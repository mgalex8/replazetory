<?php

namespace App\Bundle\YamlReplacerParser\Traits;

use App\Bundle\YamlReplacerParser\ContentFiltrator;
use App\Bundle\YamlReplacerParser\Filters\ClearfxTagImageContentFilter;
use App\Bundle\YamlReplacerParser\Filters\GetTextContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrContentFilter;
use App\Bundle\YamlReplacerParser\Filters\MixerBrSpecialContentFilter;
use App\Bundle\YamlReplacerParser\Filters\RemoveScriptContentFilter;
use App\Bundle\YamlReplacerParser\Filters\RemoveXPathContentFilter;
use App\Bundle\YamlReplacerParser\Filters\SynonimizerContentFilter;
use App\Bundle\YamlReplacerParser\Filters\TrimContentFilter;

/**
 * Trait ContentFiltratorSetup
 */
trait ContentFiltratorSetup
{

    /**
     * @return void
     */
    protected function create_filtrator()
    {
        $this->filtrator = new ContentFiltrator();
        $this->filtrator->setFilter(new MixerBrSpecialContentFilter());
        $this->filtrator->setFilter(new MixerBrContentFilter());
        $this->filtrator->setFilter(new TrimContentFilter());
        $this->filtrator->setFilter(new GetTextContentFilter());
        $this->filtrator->setFilter(new SynonimizerContentFilter());
        $this->filtrator->setFilter(new RemoveScriptContentFilter());
        $this->filtrator->setFilter(new ClearfxTagImageContentFilter());
        $this->filtrator->setFilter(new RemoveXPathContentFilter());
    }
}