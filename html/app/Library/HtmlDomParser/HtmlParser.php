<?php
namespace App\Library\HtmlDomParser;

use DOMWrap\Document;
use DOMWrap\Element;
use DOMWrap\NodeList;
use App\Library\HtmlDomParser\Contracts\AbstractParser;
use App\Library\HtmlDomParser\Options\HtmlParserOptions;

class HtmlParser extends AbstractParser
{

    /**
     * @var Document
     */
    protected $dom;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var bool
     */
    protected $use_cache;

    /**
     * @var string
     */
    protected $cache_group;

    /**
     * @var \DateTimeInterface|\DateInterval|int|null
     */
    protected $cache_ttl;

    /**
     * HtmlParser constructor.
     * @param string $url
     * @param HtmlParserOptions|null $options
     */
    public function __construct(string $url = '', ?HtmlParserOptions $options = null)
    {
        $this->use_cache    = !is_null($options) && $options->use_cache ?: true;
        $this->cache_group  = !is_null($options) && $options->cache_group ?: 'parser.html.dom.';
        $this->cache_ttl    = !is_null($options) && $options->cache_ttl ?: 3600 * 24;

        parent::__construct($url, $options);
    }

    /**
     * @param string $url
     * @param bool $set_html
     * @return string
     */
    public function extractDocument(string $url = '', bool $set_html = true) : string
    {
        /**
         * If url is empty set new url from function parameter
         */
        if (! empty($url)) {
            $this->setUrl($url);
        }

        /**
         * Request document and get $response
         */
        $response = $this->get( $this->url() );

        /**
         * Create DOM Document and set HTML from $response
         */
        if ($set_html) {
            $this->setHtml($response);
        }

        return $response;
    }

    /**
     * @param string $html
     * @return void
     */
    public function setHtml(string $html) : void
    {
        $this->dom = new Document();
        $this->dom->setHtml($html);
    }

    /**
     * @param string $html
     * @return string
     */
    public function getHtml() : string
    {
        return $this->dom->getHtml();
    }

    /**
     * @return Document
     */
    public function dom() : Document
    {
        return $this->dom;
    }

    /**
     * @param Document $dom
     * @return void
     */
    public function setDom(Document $dom)
    {
        $this->dom = $dom;
    }

    /**
     * @param Document|Element $html
     * @param string $selector
     * @return void
     */
    public function provideSelector(string $selector)
    {
        $nodes = $this->dom;

        $parts = preg_split('/[\s\>\s]+|[\>\s]+|[\s\>]+|[\s]+/', $selector);

        $i = 0;
        foreach ($parts as $prt) {
            $prt = trim($prt);
            /**
             * Filter element selector by attributes like `tag[name=test]`
             */
            if (false !== strpos($prt, '[')) {
                $prtArr = explode("[", $prt);
                $attributes = explode(',', rtrim($prtArr[1], "]"));
                foreach ($attributes as $attr) {
                    $attr = trim($attr);
                    $nodes = $nodes->filter('[' . $attr . ']');
                }
            }
            /**
             * Psevdo element selector like `div:eq(0)`, `tag:not(.class)`, tag:parent etc...
             */
            elseif (false !== strpos($prt, ':')) {
                $prtArr = explode(':', $prt);
                $elem = $prtArr[0];
                $nodes = $nodes->find($elem);
                for ($ind = 0; $ind < count($prtArr); $ind++) {
                    $pse = explode('(', $prtArr[$ind]);
                    // :: once
                    if ($pse == 'eq') {
                        $explodes = explode("eq(", $prt);
                        $value = rtrim($explodes[1], ")");
                        $nodes = $nodes->eq($value);
                    } elseif ($pse == 'first') {
                        $nodes = $nodes->first();
                    }
                    // :: collection
                    elseif ($pse == 'not') {
                        $explodes = explode("not(", $prt);
                        $value = rtrim($explodes[1], ")");
                        $nodes = $nodes->not($value);
                    } elseif ($pse == 'has') {
                        $explodes = explode("has(", $prt);
                        $value = rtrim($explodes[1], ")");
                        $nodes = $nodes->has($value);
                    } elseif ($pse == 'contains') {
                        $explodes = explode("contains(", $prt);
                        $value = rtrim($explodes[1], ")");
                        $value = trim($value, '"\'');
                        $nodes = $nodes->contents($value);
                    } elseif ($pse == 'parent') {
                        $explodes = explode("parent", $prt);
                        $value = $explodes[1];
                        $nodes = $nodes->parent($value);
                    }
                }
            }
            else {
                $nodes = $nodes->find($prt);
            }
            $i++;
        }
        return $nodes;
    }

    /**
     * @param $node
     * @param string $selector
     * @param string $url
     * @return string
     */
    public function findHtml($node, string $selector) : string
    {
        try {
            $elem = $node->find($selector)->first()->html();
        } catch (\Throwable|\Exception $e) {
            $elem = null;
            $this->saveLog($node, $selector);
        }
        return $elem;
    }

    /**
     * @param $node
     * @param string $selector
     * @param string $url
     * @return NodeList
     */
    public function findInNode($node, string $selector) : NodeList
    {
        try {
            $elem = $node->find($selector);
        } catch (\Throwable | \Exception $e) {
            $elem = null;
            $this->saveLog($node, $selector);
        }
        return $elem;
    }

    /**
     * @return null|string
     */
    public function findContainerName()
    {
        // get containers
        $data = [];
        foreach ($this->getSelectorContainerNames() as $selector) {
            if ($this->dom()->has($selector)) {
                $nx = $this->dom()->find($selector);
                $count = $nx->count();
                if ($count == 1) {
                    $html = $nx->first()->html();
                    $text = $nx->first()->text();
                    $data[$selector] = [
                        'selector' => $selector,
                        'count' => $count,
                        'length_html' => strlen($html),
                        'length_text' => strlen($text),
                    ];
                } elseif ($count > 1) {
                    // find parents and probabilities
                }
            }
        }
        if (count($data) > 0) {
            $collection = collect($data);

            $elementByLengthHtml = $collection->sortBy('length_html')->first();
            $elementByLengthText = $collection->sortBy('length_text')->first();
            if ($elementByLengthHtml['selector'] == $elementByLengthText['selector']) {
                return (string)$elementByLengthHtml['selector'];
            } else {
                return (string)$elementByLengthText['selector'];
            }
        }
        return null;
    }

    /**
     * @return array
     */
    protected function getSelectorContainerNames()
    {
        return [
            '.container',
            '.content',
            '.post',
            'article',
            'section',
            'body',
        ];
    }

    /**
     * @param $node
     * @param $selector
     */
    protected function saveLog($node, string $selector) : void
    {
        //Log::warning(sprintf("Element not found: %s", serialize(['url' => $this->url, 'selector' => $selector, 'node' => $node])));
    }

}
