<?php

namespace App\Library\PRCY;

use DOMWrap\Element;
use DOMWrap\NodeList;

class XPathFounder
{

    /**
     * @var string
     */
    protected $xpath = '';

    /**
     * @var \DOMWrap\NodeList
     */
    protected $nodes;

    /**
     * @var \DOMWrap\Element
     */
    protected $element;

    /**
     * @param \DOMWrap\Element $element
     * @param string $xpath
     */
    public function __construct(string $xpath = '')
    {
        $this->setXPath($xpath);
    }

    /**
     * @return string
     */
    public function id() : string
    {
        return uniqid('_');
    }

    /**
     * @return string
     */
    public function text() : string
    {
        return $this->nodes instanceof NodeList && $this->nodes->count() > 0 ? $this->nodes->eq(0)->text() : '';
    }

    /**
     * @return string
     */
    public function html() : string
    {
        return $this->nodes instanceof NodeList && $this->nodes->count() > 0 ? $this->nodes->eq(0)->html() : '';
    }

    /**
     * @return string
     */
    public function has() : bool
    {
        return $this->nodes instanceof NodeList && $this->nodes->count() > 0;
    }

    /**
     * @param mixed $value_true
     * @param mixed $value_false
     * @return mixed
     */
    public function result($value_true, $value_false)
    {
        return $this->nodes instanceof NodeList && $this->nodes->count() > 0 ? $value_true : $value_false;
    }

    /**
     * @param string $xpath
     * @return XPathFounder
     * @throws \Exception
     */
    public function child(string $xpath) : XPathFounder
    {
        if ($this->nodes instanceof NodeList) {
            $this->nodes = $this->nodes->findXPath($xpath);
            $this->element = $this->nodes->first();
        } else {
            $this->nodes = $this->element->findXPath($xpath);
            $this->element = $this->nodes->first();
        }

        return $this;
    }


    /**
     * @param string $name
     * @return bool
     */
    public function attr(string $name): string
    {
        return $this->nodes->first()->getAttr($name);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $input
     * @return bool
     * @throws \Exception
     */
    public function is($input): bool
    {
        return $this->element->is($input);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $input
     * @return NodeList
     */
    public function not($input): NodeList
    {
        return $this->element->not($input);
    }

    /**
     * @return NodeList|null
     */
    public function first()
    {
        return $this->nodes->first();
    }

    /**
     * @param string|NodeList|\DOMNode|callable $input
     * @return NodeList
     */
    public function filter($input): NodeList
    {
        return $this->nodes->filter($input);
    }

//    /**
//     * @param string|NodeList|\DOMNode|callable $input
//     * @return NodeList
//     */
//    public function has($input): NodeList
//    {
//        return $this->element->has($input);
//    }

    /**
     * @param string|NodeList|\DOMNode|callable $selector
     * @return \DOMNode|null
     */
    public function preceding($selector = null): ?\DOMNode
    {
        return $this->element->preceding(null, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $selector
     * @return NodeList
     */
    public function precedingAll($selector = null): NodeList
    {
        return $this->element->precedingUntil(null, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $input
     * @param string|NodeList|\DOMNode|callable $selector
     * @return NodeList
     */
    public function precedingUntil($input = null, $selector = null): NodeList
    {
        return $this->element->precedingUntil($input, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $selector
     * @return \DOMNode|null
     */
    public function following($selector = null): ?\DOMNode
    {
        return $this->element->following($selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $selector
     * @return NodeList
     */
    public function followingAll($selector = null): NodeList
    {
        return $this->element->followingAll($selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $input
     * @param string|NodeList|\DOMNode|callable $selector
     * @return NodeList
     */
    public function followingUntil($input = null, $selector = null): NodeList
    {
        return $this->element->followingUntil($input, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $selector
     * @return NodeList
     */
    public function siblings($selector = null): NodeList
    {
        return $this->element->siblings($selector);
    }

    /**
     * NodeList is only array like. Removing items using foreach() has undesired results.
     * @return NodeList
     */
    public function children(): NodeList
    {
        return $this->element->children();
    }

    /**
     * @param string|NodeList|\DOMNode|callable $selector
     * @return Element|NodeList|null
     */
    public function parent($selector = null)
    {
        return $this->element->parent($selector);
    }

    /**
     * @param int $index
     * @return \DOMNode|null
     */
    public function eq(int $index): ?\DOMNode
    {
        return $this->element->eq($index);
    }

    /**
     * @param string $selector
     * @return NodeList
     */
    public function parents(string $selector = null): NodeList
    {
        return $this->element->parents($selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $input
     * @param string|NodeList|\DOMNode|callable $selector
     * @return NodeList
     */
    public function parentsUntil($input = null, $selector = null): NodeList
    {
        return $this->element->parentsUntil($input, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode|callable $input
     * @return Element|NodeList|null
     */
    public function closest($input)
    {
        return $this->element->closest($input);
    }

    /**
     * NodeList is only array like. Removing items using foreach() has undesired results.
     *
     * @return NodeList
     */
    public function contents(): NodeList
    {
        return $this->element->contents();
    }

    /**
     * @return XPathFounder
     */
    public function then()
    {
        return $this;
    }

    /**
     * @return void
     */
    protected function setNodes(Element $element, string $xpath): void
    {
        $this->nodes = $element->findXPath($xpath);
    }

    /**
     * @param Element $element
     * @return XPathFounder
     */
    public function setElement(Element $element) : XPathFounder
    {
        $this->element = $element;
        $this->setNodes($this->element, $this->xpath);

        return $this;
    }

    /**
     * @return Element
     */
    public function getElement(): Element
    {
        return $this->element;
    }

    /**
     * @param string $xpath
     * @return XPathFounder
     */
    public function setXPath(string $xpath) : XPathFounder
    {
        $this->xpath = trim($xpath);

        return $this;
    }

    /**
     * @return string
     */
    public function getXPath(): string
    {
        return $this->xpath;
    }

}
