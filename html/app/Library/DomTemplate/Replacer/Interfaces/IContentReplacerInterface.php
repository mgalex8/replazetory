<?php
namespace App\Library\DomTemplate\Replacer\Interfaces;

/**
 * IContentReplacerInterface interface
 */
interface IContentReplacerInterface
{

    /**
     * Get XPath string for DOM search
     * @return string
     */
    public function getXPath() : string;

    /**
     * Get template name
     * @return string
     */
    public function getTemplateName() : string;

    /**
     * Get current replacer index
     * @return int
     */
    public function getCurrentIndex() : int;

    /**
     * Increment replacer index to +1
     * @return int
     */
    public function incrementIndex() : int;

    /**
     * Return current template with last replaced index
     * @return string
     */
    public function getCurrentTemplate() : string;

    /**
     * Calc RegExp and get index from template
     * @param string $template
     * @return int|null
     */
    public function getIndexFromTemplate(string $template): ?int;

    /**
     * Get text from \DOMNode element
     * @param \DOMNode $element
     * @return string
     */
    public function getTextFromElement(\DOMNode $element) : string;

    /**
     * Check template by RegExp
     * @param string $template
     * @return bool
     */
    public function isTemplate(string $template) : bool;

    /**
     * Set all delimiters for template
     * @param string $start_delimiter
     * @param string $end_delimiter
     * @param string $index_delimiter
     * @return void
     */
    public function setDelimiters(string $start_delimiter = '*', string $end_delimiter = '*', string $index_delimiter = '#,') : void;

    /**
     * Set template parameter start_delimiter
     * @param string $start_delimiter
     */
    public function setStartDelimiter(string $start_delimiter): void;

    /**
     * Set template parameter end_delimiter
     * @param string $end_delimiter
     */
    public function setEndDelimiter(string $end_delimiter): void;

    /**
     * Set template parameter index_delimiter
     * @param string $index_delimiter
     */
    public function setIndexDelimiter(string $index_delimiter): void;

    /**
     * Get template parameter start_delimiter
     * repeat symbols if parameter $repeat > 0
     * ecranized with '\' if parameter $ecranized is true
     * @param int $repeat
     * @param bool $ecranized
     * @return string
     */
    public function getStartDelimiter(int $repeat = 0, bool $ecranized = false): string;

    /**
     * Get template parameter end_delimiter
     * repeat symbols if parameter $repeat > 0
     * ecranized with '\' if parameter $ecranized is true
     * @param int $repeat
     * @param bool $ecranized
     * @return string
     */
    public function getEndDelimiter(int $repeat = 0, bool $ecranized = false): string;

    /**
     * Get template parameter index_delimiter
     * repeat symbols if parameter $repeat > 0
     * ecranized with '\' if parameter $ecranized is true
     * @param int $repeat
     * @param bool $ecranized
     * @return string
     */
    public function getIndexDelimiter(int $repeat = 0, bool $ecranized = false): string;

    /**
     * Check \DOMNode $element as needle of replace
     * and return bool value if true
     * @param \DOMNode $element
     * @return bool
     */
    public function needReplace(\DOMNode $element) : bool;

    /**
     * Replace text in \DOMNode $element
     * @param \DOMNode $element
     * @param string $newText
     * @param \DOMDocument|null $dom
     * @return void
     */
    public function replaceText(\DOMNode &$element, string $newText, \DOMDocument &$dom = null) : void;

    /**
     * Replace \DOMNode $element into \DOMNode $newElement
     * @param \DOMNode $element
     * @param \DOMNode $newElement
     * @param \DOMDocument|null $dom
     * @return void
     */
    public function replaceElement(\DOMNode &$element, \DOMNode $newElement, \DOMDocument &$dom = null) : void;

    /**
     * Replace $element into $newElementOrString
     * if $newElementOrString type of string then calc this function self::replaceText()
     * if $newElementOrString type of \DOMNode then calc this function self::replaceElement()
     * @param \DOMNode $element
     * @param \DomNode|string|int|null $newElementOrString
     * @param \DOMDocument|null $dom
     * @return string
     */
    public function replace(\DOMNode &$element, $newElementOrString = null, ?\DOMDocument &$dom = null) : string;

}
