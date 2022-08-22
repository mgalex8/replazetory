<?php
namespace App\Library\DomTemplate\Replacer;

use DOMWrap\Element;
use DOMWrap\Text;
use App\Library\DomTemplate\Replacer\Interfaces\IContentReplacerInterface;

/**
 * TagReplacer class
 */
class TagReplacer extends AbstractReplacer implements IContentReplacerInterface
{

    /**
     * @var string
     */
    protected $xpath;

    /**
     * @var string
     */
    protected $tpl;

    protected $ignores_tag_names = [
        'html', 'head', 'body', 'meta', 'title', 'style', 'script', 'link',
    ];

    /**
     * @return string
     */
    protected function getIgnoresTagNames() {
        $ignores = [];
        foreach ($this->ignores_tag_names as $tag_name) {
            $ignores[] = 'local-name()!=\''.$tag_name.'\'';
        }
        return $ignores ? implode(' and ', $ignores) : '';
    }

    /**
     * @return string
     */
    public function getXPath() : string {
        return $this->xpath ?: "//*[".$this->getIgnoresTagNames()."]";
    }

    /**
     * @return string
     */
    public function getTemplateName() : string {
        return $this->tpl ?: 'TAG';
    }

    /**
     * @param \DOMNode $element
     * @return string
     */
    public function getTextFromElement(\DOMNode $element) : string {
//        dump([get_class($element), (string) $element, trim($element->getText()), $element->textContent, $element->getNodePath(), $element]);
//        return trim($element->getText());
        return $element->getText() !== null ? $element->getText() : '';
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

        return parent::needReplace($element)
            && !empty($element->localName)
//            && ! $element->hasChildNodes()
            && ! $this->isTemplate($this->getTextFromElement($element));

//        if ($element->getAttribute('class') == 'feedback-link') {
//        var_dump($element);
        if ($element->hasChildNodes()) {
            $nodes = [];
            foreach ($element->childNodes as $node) {
                $nodes[] = $node;
            }
        }
//
        dump([
            $element->textContent !== null ? (string) $element : '',
            $this->getTextFromElement($element),
            parent::needReplace($element) ,
            !empty($element->localName) ,
            ! $element->hasChildNodes() ,
            ! $this->isTemplate($this->getTextFromElement($element)),
            $element->hasChildNodes() ? array_map(function($e) { return [(string) $e, $e]; }, (array) $nodes) : null,
            $element
        ]);

        return $boolen;
    }

}
