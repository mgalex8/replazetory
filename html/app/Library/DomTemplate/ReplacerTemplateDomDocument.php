<?php
namespace App\Library\DomTemplate;

use DOMWrap\Document;
use DOMWrap\Element;
use App\Library\DomTemplate\Exception\DOMDocumentIsNULLException;
use App\Library\DomTemplate\Exception\UnregisteredReplacerNameException;
use App\Library\DomTemplate\Replacer\AttributeTitleReplacer;
use App\Library\DomTemplate\Replacer\MetaArticleAuthorReplacer;
use App\Library\DomTemplate\Replacer\AttributeAltReplacer;
use App\Library\DomTemplate\Replacer\HtmlCommentaryReplacer;
use App\Library\DomTemplate\Replacer\MetaAppleMobileWebAppTitleReplacer;
use App\Library\DomTemplate\Replacer\MetaDescriptionReplacer;
use App\Library\DomTemplate\Replacer\MetaKeywordsReplacer;
use App\Library\DomTemplate\Replacer\MetaOgDescriptionReplacer;
use App\Library\DomTemplate\Replacer\MetaOgTitleReplacer;
use App\Library\DomTemplate\Replacer\TagAReplacer;
use App\Library\DomTemplate\Replacer\TagReplacer;
use App\Library\DomTemplate\Replacer\TextReplacer;
use App\Library\DomTemplate\Replacer\TitleReplacer;
use App\Library\DomTemplate\Replacer\Interfaces\IContentReplacerInterface;

/**
 * GoogleTranslate class
 */
class ReplacerTemplateDomDocument
{

    /**
     * @var Document
     */
    protected $dom;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var ReplacerManager
     */
    protected $replacers;

    /**
     * @var array
     */
    protected $templates = [];

    /**
     * Class Contructor.
     *
     * @param string $content
     * @param array $options
     */
    public function __construct(string $content = '', array $options = [])
    {
        /** Set content **/
        if ($content) {
            $this->createDom($content);
        }
        /** Set options **/
        $this->options = $options;

        /** Set replacers **/
        $this->initReplacers();
    }

    /**
     * Initialize default replacers for this class
     * @return void
     */
    protected function initReplacers() : void
    {
        $this->replacers()->setReplacersMany([
            'title' => new TitleReplacer(),                                         // <title>和自己和解 改变自己_手机新浪网</title>
//            'meta.keywords' => new MetaKeywordsReplacer(),                              // <meta name="keywords" content="傅首尔,婚姻,改变,原生家庭,sina.cn">
//            'meta.description' => new MetaDescriptionReplacer(),                        // <meta name="description" content="只有真正和自己和解，改变自己，才能彻底地走出原生家庭衍生出的硬伤。">
//            'og.title' => new MetaOgTitleReplacer(),                                    // <meta property="og:description" content="和自己和解 改变自己" />
//            'og.description' => new MetaOgDescriptionReplacer(),                        // <meta property="og:url" content="https://eladies.sina.cn/feel/answer/2020-04-28/detail-iirczymi8418057.d.html" />
//            'article.author' => new MetaArticleAuthorReplacer(),                        // <meta property="article:author" content="彬彬有理脱口秀"/>
//            'apple-mobile-web-app-title' => new MetaAppleMobileWebAppTitleReplacer(),   // <meta name="apple-mobile-web-app-title" content="新浪移动_手机新浪网">
//            'attr.alt' => new AttributeAltReplacer(),                                   // <img src="//img.cn/img.png" alt="彬彬有理脱口秀" />
//            'attr.title' => new AttributeTitleReplacer(),                               // <a href="/" title="彬彬有理脱口秀" />
//            'script.content' => new ScriptCOntentReplacer(),                          // <script>var a={"__title":"和自己和解 改变自己_手机新浪网","__content":"只有真正和自己和解，改变自己，才能彻底地走出原生家庭衍生出的硬伤"}</script>
//            'html.commentary' => new HtmlCommentaryReplacer(),                          // <!--标题_s-->
//            'tag.text' => new TextReplacer(),                                        // <div> 底地走 <i>出原生家</i> 和自 </div>
//            'tags.all' => new TagReplacer(),                                        // <div> 底地走 <i>出原生家</i> 和自 </div>
//            'tag.a' => new TagAReplacer(),                                        // <div> 底地走 <i>出原生家</i> 和自 </div>
        ]);
    }

    /**
     * Make content from base and replace all installed replacers in ReplacerManager component
     * @param string $content
     * @param array $options
     * @return mixed
     * @throws DOMDocumentIsNULLException
     */
    public function replace(string $content = '', array $options = [])
    {
        $this->options = $options ?: $this->options;

        if ($content) {
            $this->createDom($content);
        }

        return $this->replaceTemplatesAll();
    }

    /**
     * @param IContentReplacerInterface|string $replacer
     * @return mixed
     * @throws DOMDocumentIsNULLException
     * @throws UnregisteredReplacerNameException
     */
    public function replaceTemplate($replacer) : string
    {
        if ($this->dom() === null) {
            throw new DOMDocumentIsNULLException(__CLASS__);
        }

        if (is_string($replacer)) {
            $replacer = $this->replacers()->getReplacerByName($replacer);
        }

        $xp = new DOMXPath($this->dom);
        $elements = $xp->query($replacer->getXPath());
        foreach ($elements as $element) {
            if ($replacer->needReplace($element)) {
                $this->setTemplate($replacer, $element, $dom);
            }
        }

        return $this->dom->saveHTML();
    }

    /**
     * @return mixed
     * @throws DOMDocumentIsNULLException
     */
    public function replaceTemplatesAll()
    {
        if ($this->dom === null) {
            throw new DOMDocumentIsNULLException(__CLASS__);
        }

//        $goddes1 = [];
//        $nodes = $dom->getElementsByTagName('*');
//        foreach ($nodes as $node) {
//            for ($child = $node->firstChild; $child; $child = $child->nextSibling) {
//                if (!($child->nodeType === XML_TEXT_NODE && trim($child->textContent))) {
//                    continue;
//                }
//            }
//        }
//        dump($goddes1);

//        $goddes3 = [];
//        //$nodes = $this->dom->getElementsByTagName('//body/*');
//        $xp = new \DOMXPath($this->dom);
//        foreach ($xp->query('//html') as $node) {
//            $goddes3[] = $this->reqAll($node);
//        }
//       dump($goddes3);

        $xp = new \DOMXPath($this->dom);
        foreach ($this->replacers()->getReplacersAll() as $replacer_name => $replacer) {
            $elements = $xp->query($replacer->getXPath());
            foreach ($elements as $element) {
                $this->replaceElementReqursive($replacer, $element, $this->dom);
            }
        }

        dump($this->getTemplatesAllGrouped());

        return $this->dom->saveHTML();
    }

    /**
     * @param \DOMNode $node
     * @return array
     */
    protected function reqursive( $node)
    {
        dump($node);
        $result = [];
        for ($child = $node->firstChild; $child; $child = $child->nextSibling) {
            $result[$child->nodeName] = $this->reqAll($node);
        }
        return $result;
    }

    /**
     * @param \DOMNodeList $nodes
     * @return array
     */
    protected function reqursiveNL(\DOMNodeList $nodes)
    {
        $result = [];
        foreach ($nodes as $node) {
            $result[$node->nodeName] = $this->reqAll($node);
        }
        return $result;
    }

    /**
     * @param $node
     * @return array
     */
    protected function reqAll($node)
    {
        if ($node instanceof \DOMNodeList && $node->count() > 0) {
            return $this->DomNodeToArray($node, $this->reqursiveNL($node));
        } elseif ($node instanceof Element || $node instanceof \DOMNode) {
            return $this->DomNodeToArray($node, $this->reqursiveNL($node->childNodes));
        } else {
            return $this->DomNodeToArray($node, null);
        }
    }


    public function DomNodeToArray($element, $children = null)
    {
        $xpath = $element->getNodePath();
        $html = '';//(string) $element;
        $text = '';//$element->getText();
        $line_number = method_exists($element, 'getLineNo') ? $element->getLineNo() : null;
        $local_name = $element->localName;
        $namespace_uri = $element->namespaceURI;
        $base_uri = $element->baseURI;
        $node_name = $element->nodeName;

        return [
            'source' => $html,
            'text' => $text,
            'xpath' => $xpath,
            'line' => $line_number,
            'local_name' => $local_name,
            'base_uri' => $base_uri,
            'namespace_uri' => $namespace_uri,
            'node_name' => $node_name,
            'element' => $element,
            'children' => $children,
        ];
    }

    /**
     * @param IContentReplacerInterface $replacer
     * @param \DOMNode $element
     * @param $dom
     * @return void
     */
    protected function replaceElementReqursive(IContentReplacerInterface $replacer, \DOMNode &$element, &$dom = null)
    {
        if ($replacer->needReplace($element)) {
            $this->setTemplate($replacer, $element, $dom);
        }
    }


    /**
     * @param IContentReplacerInterface $replacer
     * @param \DOMNode $element
     * @param \DOMDocument $dom
     * @return void
     */
    protected function setTemplate(IContentReplacerInterface $replacer, \DOMNode &$element, \DOMDocument &$dom = null) : void
    {
        $template = null;
        $cloned_rep = clone $replacer;
        $cloned_elem = clone $element;

        $template_name = $replacer->getTemplateName();
        $index = $replacer->getCurrentIndex();
        $xpath = $element->getNodePath();
        $html = (string) $element;
        $text = $replacer->getTextFromElement($element);
        $line_number = $element->getLineNo();
        $local_name = $element->localName;
        $namespace_uri = $element->namespaceURI;
        $base_uri = $element->baseURI;
        $node_name = $element->nodeName;;

        try {
            if ($replacer->needReplace($element) ) {
                $template = $replacer->replace($element, null, $dom);
                $replaced_is_true = true;
            } else {
                $replaced_is_true = false;
            }
        } catch (\Exception|\Throwable $e) {
            $replaced_is_true = false;
        }

        if ($replaced_is_true) {
            $this->templates[ $template_name ][ $index ] = [
                'template' => $template,
                'source' => $html,
                'text' => $text,
                'xpath' => $xpath,
                'line' => $line_number,
                'local_name' => $local_name,
                'base_uri' => $base_uri,
                'namespace_uri' => $namespace_uri,
                'node_name' => $node_name,
                'element' => $cloned_elem,
                'replacer' => $cloned_rep,
            ];
        }
    }

    /**
     * @return array
     */
    public function getTemplatesAll() : array
    {
        $tpl_all = [];
        foreach ($this->templates as $template_name => $templates) {
            foreach ($templates as $template) {
                $tpl_all[] = $template;
            }
        }
        return $tpl_all;
    }

    /**
     * @return array
     */
    public function getTemplatesAllGrouped() : array
    {
        return $this->templates;
    }

    /**
     * @return ReplacerManager
     */
    public function replacers()
    {
        if ($this->replacers === null) {
            $this->replacers = new ReplacerManager();
        }
        return $this->replacers;
    }

    /**
     * @return ?Document
     */
    public function dom() : ?Document
    {
        return $this->dom;
    }

    /**
     * @param Document $dom
     * @return void
     */
    public function setDom(\DOMDocument $dom): void
    {
        $this->dom = $this->castDomDocumentIntoDocument($dom);
    }

    /**
     * Create DOM Document
     * @param string $content
     * @return Document
     */
    protected function createDom(string $content) : void
    {
        $this->dom = new Document();
        $this->dom->setHtml($content);
    }

    /**
     * @param string $content
     * @return ReplacerTemplateDomDocument
     */
    public function setHtml(string $content) : ReplacerTemplateDomDocument
    {
        $this->createDom($content);
        return $this;
    }

    /**
     * @param \DOMDocument|null $node
     * @return DOMWrap\Document|null
     */
    protected function castDomDocumentIntoDocument(?\DOMDocument &$dom = null) : ?Document {
        return $dom ?: null;
    }

    /**
     * @param DOMWrap\Document|null $node
     * @return \DOMDocument|null
     */
    protected function castDocumentIntoDOMDocument(?Document &$dom = null) : ?\DOMDocument {
        return $dom ?: null;
    }

}
