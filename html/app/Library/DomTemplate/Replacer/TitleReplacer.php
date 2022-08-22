<?php
namespace App\Library\DomTemplate\Replacer;

use DOMWrap\Comment;
use DOMWrap\Element;
use App\Library\DomTemplate\Replacer\Interfaces\IContentReplacerInterface;

/**
 * TitleReplacer class
 */
class TitleReplacer extends AbstractReplacer implements IContentReplacerInterface
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
        return $this->xpath ?: '//title';
    }

    /**
     * @return string
     */
    public function getTemplateName() : string {
        return $this->tpl ?: 'TITLE';
    }

    /**
     * @param \DOMNode $element
     * @return string
     */
    public function getTextFromElement(\DOMNode $element) : string {
        if (property_exists($element, 'textContent')) {
            return $element->textContent !== null ? $element->textContent : '';
        } elseif (method_exists($element, 'getText')) {
            return $element->getText() !== null ? $element->getText() : '';
        } else {
            throw new \Exception(sprintf('Node element %s has not method \'getHtml()\' and not has property \'textContent\'', is_object($element) ? get_class($element) : gettype($element)));
        }
        return $element->getText() !== null ? $element->getText() : '';
    }

    /**
     * @param \DOMNode $element
     * @param string $newText
     * @param \DOMDocument|null $dom
     * @return void
     */
    public function replaceText(\DOMNode &$element, string $newText, \DOMDocument &$dom = null) : void {
        if (method_exists($element, 'setText')) {
            $element->setHtml($newText);
        } elseif (property_exists($element, 'textContent')) {
            $element->textContent = $newText;
        } else {
            throw new \Exception(sprintf('Node element %s has not method \'getHtml()\' and not has property \'textContent\'', is_object($element) ? get_class($element) : gettype($element)));
        }
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
