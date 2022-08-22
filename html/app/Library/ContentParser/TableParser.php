<?php

namespace VisymoGutenberg\ContentParser;

use VisymoGutenberg\ContentParser\Component\HTMLClearComponent;
use DOMWrap\Document;

class TableParser
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $tables;

    /**
     * @var
     */
    protected $dom;

    /**
     * @var HTMLClearComponent
     */
    protected $clearComponent;

    /**
     * Contructor.
     *
     * @param string $content
     * @param array $options
     */
    public function __construct(string $content = '', array $options = [])
    {
        $this->tables = [];
        $this->content = $content;
        $this->options = $options;
        $this->clearComponent = new HTMLClearComponent();
    }

    /**
     * Make content from base
     *
     * @param array $options
     * @return mixed|string
     */
    public function make(string $content = '', array $options = [])
    {
        $this->content = $content ?: $this->content;
        $this->options = $options ?: $this->options;
        $this->tables = [];
        $this->createDom();

        $container = $this->dom->find('table');
        foreach ($container as $node) {
            $this->tables[] = $this->parseTable((string) $node, $this->options);
        }

        return $this->tables;
    }

    /**
     * @param string $content
     * @return void
     */
    protected function parseTable(string $content, array $options = [])
    {
        $table = $content;

        if (isset($options['clear']) && $options['clear']) {
            $table = $this->clearfix($table, $options);
        }

        if (isset($options['class_names']) && $options['class_names']) {
            $table = $this->addStyles($table, $options);
        }

        return $table;
    }

    /**
     * Clear all unusable arguments
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    protected function clearfix(string $content, array $options = [])
    {
        $options = $options ?: $this->options;
        return $this->clearComponent()->clearTable($content, $options);
    }

    /**
     * Add classes to content
     * @param string $content
     * @param array $options
     * @return mixed
     */
    protected function addStyles(string $content, array $options = [])
    {
        $dom = new \DOMDocument;
        $dom->loadHTML($content);
        $xp = new \DOMXpath($dom);
        foreach ($options['class_names'] as $tag => $class) {
            foreach ($xp->query('//'.$tag) as $node) {
                $node->setAttribute('class', $class);
            }
        }
        foreach ($xp->query('//a[not(@style)]') as $node) {
            $node->setAttribute('style', 'font-weight:bold; color:#00f');
        }

        return $dom->saveHTML();
    }

    /**
     * Create DOM Document
     * @return void
     */
    protected function createDom()
    {
        $this->dom = new Document();
        $this->dom->setHtml($this->content);
    }

    /**
     * Get data as array
     * @return array
     */
    public function toArray() : array
    {
        return $this->tables;
    }

    /**
     * Get data as string
     * @return string
     */
    public function toString() : string
    {
        $result = '';
        foreach ($this->tables as $table) {
            $result .= $table;
        }
        return $result;
    }

    /**
     * @return HTMLClearComponent
     */
    public function clearComponent() : HTMLClearComponent
    {
        return $this->clearComponent;
    }

}
