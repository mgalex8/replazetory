<?php

namespace VisymoGutenberg\ContentParser\Component;

class HTMLClearComponent
{

    /**
     * @var array
     */
    protected $options;

    /**
     * Contructor.
     * @param array $options
     */
    public function __construct(string $content = '', array $options = [])
    {
        $this->content = $content;
        $this->options = $options;
    }

    /**
     * Clear content
     *
     * @param string $content
     * @param array $options
     * @param string $type
     * @return string
     */
    public function clear(string $content, array $options = [], string $type = '') : string
    {
        $options = $options ?: $this->options;
        if ($type == 'table') {
            return $this->clearTable($options);
        } elseif ($type == 'a') {
            return $this->clearLink($options);
        } else {
            return $this->clearAll($options);
        }
    }

    /**
     * Clear any content
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public function clearAll(string $content, array $options = []) : string
    {
        $content = $content ?: $this->content;
        $options = $options ?: $this->options;

        $content = preg_replace("/<([a-z][a-z0-9]*)[^<|>]*?(\/?)>/si",'<$1$2>', $content);

        return $content;
    }

    /**
     * Clear table content
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public function clearTable(string $content, array $options = []) : string
    {
        $content = $content ?: $this->content;
        $options = $options ?: $this->options;

        if (isset($options['clear']) && $options['clear']) {
//            $dom = new DOMDocument;                 // init new DOMDocument
//            $dom->loadHTML($html);                  // load HTML into it
//            $xpath = new DOMXPath($dom);            // create a new XPath
//            $nodes = $xpath->query('//*[@style]');  // Find elements with a style attribute
//            foreach ($nodes as $node) {              // Iterate over found elements
//                $node->removeAttribute('style');    // Remove style attribute
//            }
//            echo $dom->saveHTML();
        }
//        if (isset($options['ignore_classes']) && $options['ignore_classes']) {
//            $content = preg_replace("/<([a-z][a-z0-9]*)[^<|>]*?(\/?)>/si",'<$1$2>', $content);
//        }
        $content = preg_replace("/<([a-z][a-z0-9]*)[^<|>]*?(\/?)>/si",'<$1$2>', $content);

        return $content;
    }

    /**
     * @param string $content
     * @param array $options
     * @return array|string|string[]|null
     */
    public function clearLink(string $content, array $options = [])
    {
        $content = $content ?: $this->content;
        $options = $options ?: $this->options;

//        $dom = new \DOMDocument;
//        $dom->loadHTML($content);
//        $xp = new \DOMXpath($dom, LIBXML_NOERROR);
//        foreach ($options['class_names'] as $tag => $class) {
//            foreach ($xp->query('//'.$tag) as $node) {
//                $node->setAttribute('class', $class);
//            }
//        }
//        foreach ($xp->query('//a[not(@style)]') as $node) {
//            $node->setAttribute('style', 'font-weight:bold; color:#00f');
//        }
//
//        $dom = new \DOMDocument;
//        @$dom->loadHTML($content, LIBXML_NOERROR);
//        $xp = new \DOMXPath($dom);
//        $nodes = $xp->query('//@*');
//        foreach ($nodes as $node) {
//            $node->parentNode->removeAttribute($node->nodeName);
//        }

//        return $dom->saveHTML();
        return $content;
    }

}
