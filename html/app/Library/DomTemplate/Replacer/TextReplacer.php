<?php
namespace App\Library\DomTemplate\Replacer;

use DOMWrap\Element;
use DOMWrap\Text;
use App\Library\DomTemplate\Replacer\Interfaces\IContentReplacerInterface;

/**
 * TextReplacer class
 */
class TextReplacer extends AbstractReplacer implements IContentReplacerInterface
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
        return $this->xpath ?: ".//*[*]/text()";
        return $this->xpath ?: ".//*[*][local-name()!='script' and local-name()!='style' and local-name()!='meta']/text()";
//        return $this->xpath ?: "//body/*[.!=''][local-name()!='script' and local-name()!='meta' and local-name()='head' and local-name()='style']";
    }

    /**
     * @return string
     */
    public function getTemplateName() : string {
        return $this->tpl ?: 'TEXT';
    }

    /**
     * @param \DOMNode $element
     * @return string
     */
    public function getTextFromElement(\DOMNode $element) : string {
//        dump([get_class($element), (string) $element, $element]);
//        return trim($element->getText());
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
