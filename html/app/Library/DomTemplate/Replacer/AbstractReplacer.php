<?php
namespace App\Library\DomTemplate\Replacer;

use DOMWrap\Comment;
use DOMWrap\DocumentType;
use DOMWrap\Element;
use DOMWrap\Text;
use App\Library\DomTemplate\Replacer\Traits\ReplacerClosures;
use App\Library\DomTemplate\Replacer\Traits\ReplacerDelimiters;

/**
 * Abstract class AbstractReplacer
 */
abstract class AbstractReplacer
{

    use ReplacerDelimiters, ReplacerClosures;

    /**
     * @var string
     */
    protected $xpath;

    /**
     * @var string
     */
    protected $tpl;

    /**
     * @var int
     */
    protected $index = 1;

    /**
     * Class Constructor.
     */
    public function __construct() {
        $this->xpath = $this->getXPath();
        $this->tpl = $this->getTemplateName();
        $this->setDelimiters('*', '*', '#');
    }

    /**
     * Set XPath string
     * @param string $xpath
     */
    public function setXPath(string $xpath): void {
        $this->xpath = $xpath;
    }

    /**
     * Set template name
     * @param string $xpath
     */
    public function setTemplate(string $template): void {
        $this->tpl = $template;
    }

    /**
     * Increment replacer index to +1
     * @return int
     */
    public function incrementIndex() : int {
        $this->index++;
        return $this->index;
    }

    /**
     * Get current replacer index
     * @return int
     */
    public function getCurrentIndex() : int {
        return $this->index;
    }

    /**
     * Get current replacer index
     * @return int
     */
    public function getNextIndex() : int {
        return $this->index + 1;
    }

    /**
     * Get current replacer index
     * @return int
     */
    public function getPreviousIndex() : int {
        return $this->index - 1;
    }

    /**
     * Set current replacer index
     * @param int $index
     * @return void
     */
    public function setCurrentIndex(int $index) : void {
        $this->index = $index;
    }

    /**
     * Get current template with name $this->tpl and current index $this->index
     * Examples:
     *      "***__TEST__#,4***"   // tpl = '__TEST__', index = 4, start_delimiter = '*", end_delimiter = '*', index_delimiter = '#'
     * @return string"
     */
    public function getCurrentTemplate() : string {
        if ($this->existsClosure('getCurrentTemplate')) {
            return $this->__callClosure('getCurrentTemplate', $this);
        }
        return sprintf('%s___%s___%s,%s,___%s', $this->getStartDelimiter(3, false), $this->getTemplateName(), $this->getIndexDelimiter(1, false), $this->getCurrentIndex(),  $this->getEndDelimiter(3, false));
    }

    /**
     * Calc RegExp and get index from template
     * @param string $template
     * @return int|null
     */
    public function getIndexFromTemplate(string $template) : ?int {
        if ($this->existsClosure('getIndexFromTemplate')) {
            return $this->__callClosure( 'getIndexFromTemplate', $this, $template);
        }
        $regexp = sprintf("/%s___%s___%s\,([\d]+)\,___%s/s", $this->getStartDelimiter(3, true), $this->getTemplateName(), $this->getIndexDelimiter( 1, true), $this->getEndDelimiter(3, true));
        preg_match($regexp, trim($template), $matches);
        return isset($matches[1]) && is_numeric($matches[1]) ? (int) $matches[1] : null;
    }

    /**
     * Check template by RegExp
     * @param string $template
     * @param int|null $index
     * @return bool
     */
    public function isTemplate(string $template, ?int $index = null) : bool {
        if ($this->existsClosure('isTemplate')) {
            return $this->__callClosure( 'isTemplate', $this, $template, $index);
        }
        if ($index !== null) {
            $regexp = sprintf("/%s___%s___%s\,%d\,___%s/s", $this->getStartDelimiter(3, true), $this->getTemplateName(), $this->getIndexDelimiter(1, true), $index, $this->getEndDelimiter(3, true));
        } else {
            $regexp = sprintf("/%s___%s___%s\,[\d]+\,___%s/s", $this->getStartDelimiter(3, true), $this->getTemplateName(), $this->getIndexDelimiter(1, true), $this->getEndDelimiter(3, true));
        }

        return preg_match($regexp, trim($template));
    }

    /**
     * Replace $element into $newElementOrString
     * sea self::replace() function for more...
     * @param \DomNode $element
     * @param \DomNode|string|int|null $newElementOrString
     * @param \DOMDocument|null $dom
     * @return void
     */
    protected function replaceTemplate(\DomNode &$element, $newElementOrString = null, ?\DOMDocument &$dom = null) : void {
        if ($newElementOrString instanceof \DomElement || $newElementOrString instanceof \DomComment) {
            $this->replaceElement($element, $newElementOrString, $dom);
        } elseif ((is_string($newElementOrString) || is_numeric($newElementOrString))) {
            $this->replaceText($element, $newElementOrString, $dom);
        } else {
            $templateText = $this->getCurrentTemplate();
            $this->replaceText($element, $templateText, $dom);
        }
        // save element
        if ($dom !== null) {
            $dom->saveHTML($element);
        } else {
            $element->saveHtml();
        }
    }

    /**
     * Replace $element into $newElementOrString
     * if $newElementOrString type of string then calc this function self::replaceText()
     * if $newElementOrString type of \DOMNode then calc this function self::replaceElement()
     * @param \DomNode $element
     * @param \DomNode|string|int|null $newElementOrString
     * @param \DOMDocument|null $dom
     * @return string
     * @throws \Exception|\Throwable
     */
    public function replace(\DomNode &$element, $newElementOrString = null, ?\DOMDocument &$dom = null) : string {
        try {
            // replace template
            $this->replaceTemplate($element, $newElementOrString, $dom);

            // get template
            try {
                $template = $this->getCurrentTemplate();
            } catch (\Exception|\Throwable $e) {
                $template = null;
            }

            // increment $this->index += 1;
            $this->incrementIndex();

        } catch (\Exception|\Throwable $e) {
            $this->incrementIndex();
            throw $e;
        }

        return $template;
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
     * Check \DOMNode $element as needle of replace
     * and return bool value if true
     * @param \DOMNode $element
     * @return bool
     */
    public function needReplace(\DOMNode $element) : bool {
        return !empty(trim($this->getTextFromElement($element)));
    }

    /**
     * Cast \DOMNode object $node into \DOMWrap\Element object class
     * @param \DOMNode|null $node
     * @return Element|null
     */
    public function castDomNodeToElement(?\DOMNode &$node = null) : ?Element {
        return $node ?: null;
    }

    /**
     * Cast \DOMNode object $node into \DOMWrap\Comment object class
     * @param \DOMNode|null $node
     * @return Comment|null
     */
    public function castDomNodeToComment(?\DOMNode &$node = null) : ?Comment {
        return $node ?: null;
    }

    /**
     * Cast \DOMNode object $node into \DOMWrap\DocumentType object class
     * @param \DOMNode|null $node
     * @return Comment|null
     */
    public function castDomNodeToDocumentType(?\DOMNode &$node = null) : ?DocumentType {
        return $node ?: null;
    }

    /**
     * Cast \DOMNode object $node into \DOMWrap\Text object class
     * @param \DOMNode|null $node
     * @return Comment|null
     */
    public function castDomNodeToText(?\DOMNode &$node = null) : ?Text {
        return $node ?: null;
    }

    /**
     * Get text from \DOMNode element
     * @param \DOMNode $element
     * @return string
     */
    abstract public function getTextFromElement(\DOMNode $element) : string;

    /**
     * Get XPath string for DOM search
     * @return string
     */
    abstract public function getXPath() : string;

    /**
     * Get template name
     * @return string
     */
    abstract public function getTemplateName() : string;

}
