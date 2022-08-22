<?php
namespace App\Library\DomTemplate\Replacer;

use App\Library\DomTemplate\Replacer\Interfaces\IContentReplacerInterface;

/**
 * MetaOgTitleReplacer class
 */
class MetaOgTitleReplacer extends AbstractReplacer implements IContentReplacerInterface
{

    /**
     * @var string
     */
    protected $xpath;

    /**
     * @var string
     */
    protected $tpl;

    /**
     * @return string
     */
    public function getXPath() : string {
        return $this->xpath ?: "//meta[@property='og:title']";
    }

    /**
     * @return string
     */
    public function getTemplateName() : string {
        return $this->tpl ?: 'META_OG_TITLE';
    }

    /**
     * @param \DOMNode $element
     * @return string
     */
    public function getTextFromElement(\DOMNode $element) : string {
        return trim($element->getAttribute('content'));
    }

    /**
     * @param \DOMNode $element
     * @param string $newText
     * @param \DOMDocument|null $dom
     * @return void
     */
    public function replaceText(\DOMNode &$element, string $newText, \DOMDocument &$dom = null) : void {
        $element->setAttribute('content', trim($newText));
    }

    /**
     * @param \DOMNode $element
     * @param \DOMNode $newElement
     * @param \DOMDocument|null $dom
     * @return void
     */
    public function replaceElement(\DOMNode &$element, \DOMNode $newElement, ?\DOMDocument &$dom = null) : void {
        $element->parentNode->replaceChild($newElement, $element);
    }

    /**
     * @param \DOMNode $element
     * @return bool
     */
    public function needReplace(\DOMNode $element): bool {
        return parent::needReplace($element) && ! $this->isTemplate($this->getTextFromElement($element));
    }

}
