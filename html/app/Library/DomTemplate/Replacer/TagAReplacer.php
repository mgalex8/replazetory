<?php
namespace App\Library\DomTemplate\Replacer;

use DOMWrap\Element;
use DOMWrap\Text;
use App\Library\DomTemplate\Replacer\Interfaces\IContentReplacerInterface;

/**
 * TagAReplacer class
 */
class TagAReplacer extends AbstractReplacer implements IContentReplacerInterface
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
        return $this->xpath ?: ".//a";
    }

    /**
     * @return string
     */
    public function getTemplateName() : string {
        return $this->tpl ?: 'TAG_A';
    }

    /**
     * @param \DOMNode $element
     * @return string
     */
    public function getTextFromElement(\DOMNode $element) : string {
        return $element->textContent;
    }

    /**
     * @param \DOMNode $element
     * @param string $newText
     * @param \DOMDocument|null $dom
     * @return void
     */
    public function replaceText(\DOMNode &$element, string $newText, \DOMDocument &$dom = null) : void {
        $element->textContent = trim($newText);
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
