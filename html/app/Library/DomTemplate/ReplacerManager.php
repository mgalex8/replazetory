<?php
namespace App\Library\DomTemplate;

use DOMWrap\Document;
use http\Exception\BadMethodCallException;
use App\Library\DomTemplate\Exception\UnregisteredReplacerNameException;
use App\Library\DomTemplate\Replacer\AbstractReplacer;
use App\Library\DomTemplate\Replacer\AttributeTitleReplacer;
use App\Library\DomTemplate\Replacer\MetaArticleAuthorReplacer;
use App\Library\DomTemplate\Replacer\AttributeAltReplacer;
use App\Library\DomTemplate\Replacer\Interfaces\IContentReplacerInterface;
use App\Library\DomTemplate\Replacer\HtmlCommentaryReplacer;
use App\Library\DomTemplate\Replacer\MetaAppleMobileWebAppTitleReplacer;
use App\Library\DomTemplate\Replacer\MetaDescriptionReplacer;
use App\Library\DomTemplate\Replacer\MetaKeywordsReplacer;
use App\Library\DomTemplate\Replacer\MetaTitleReplacer;
use App\Library\DomTemplate\Replacer\MetaOgDescriptionReplacer;
use App\Library\DomTemplate\Replacer\MetaOgTitleReplacer;
use App\Library\DomTemplate\Replacer\TextReplacer;

/**
 * GoogleTranslate class
 */
class ReplacerManager
{

    /**
     * @var array
     */
    protected $replacers = [];

    /**
     * @param array $replacers
     */
    public function __construct(array $replacers = [])
    {
        $this->setReplacersMany($replacers);
    }


    /**
     * @param string $name
     * @param IContentReplacerInterface $replacer
     * @return ReplacerManager
     */
    public function setReplacer(string $name, IContentReplacerInterface $replacer) : ReplacerManager
    {
        $this->replacers[ $name ] = $replacer;
        return $this;
    }

    /**
     * @return array
     */
    public function getReplacersAll() : array
    {
        return $this->replacers;
    }

    /**
     * @param string $replacer_name
     * @return IContentReplacerInterface
     * @throws UnregisteredReplacerNameException
     */
    public function getReplacerByName(string $name) : IContentReplacerInterface
    {
        if (! isset($this->replacers[ $name ])) {
            throw new UnregisteredReplacerNameException(null, $name);
        }
        return $this->replacers[ $name ];
    }

    /**
     * @param array $replacers
     * @return void
     */
    public function setReplacersMany(array $replacers = []): void
    {
        $this->replacers = [];
        $this->insertReplacersMany($replacers);
    }

    /**
     * @param array $replacers
     * @return void
     */
    public function insertReplacersMany(array $replacers = []): void
    {
        foreach($replacers as $replacer_name => $replacer) {
            if (! $replacer instanceof IContentReplacerInterface) {
                throw new BadMethodCallException();
            }
            $this->replacers[$replacer_name] = $replacer;
        }
    }

    /**
     * @param IContentReplacerInterface $replacer
     * @param \DOMNode $element
     * @return bool
     */
    public function needReplace(IContentReplacerInterface $replacer, \DOMNode $element) : bool
    {
        return $replacer->needReplace($element);
    }

    /**
     * @param \DOMNode $element
     * @return bool
     */
    public function checkHasReplacesTemplatesAll(\DOMNode $element) : bool
    {
        foreach ($this->replacers as $replacer) {
            if ($replacer->isTemplate($replacer->getTextFromElement($element))) {
                return false;
            }
        }
        return true;
    }


}
