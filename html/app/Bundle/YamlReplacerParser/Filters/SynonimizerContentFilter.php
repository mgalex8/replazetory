<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;
use App\Library\Synonimizer\Filters\ExtecranizeTagsContentFilter;
use App\Library\Synonimizer\Filters\GetTextContentFilter;
use App\Library\Synonimizer\Synonimizer;
use DOMWrap\Document;

/**
 * Class GetTextContentFilter
 */
class SynonimizerContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'synonimizer';

    /**
     * @var Synonimizer
     */
    protected $synonimizer;

    /**
     * Constructor class
     */
    public function __construct()
    {
        $this->synonimizer = new Synonimizer();
        $this->synonimizer->setFilter(new GetTextContentFilter());
        $this->synonimizer->setFilter(new ExtecranizeTagsContentFilter());
        parent::__construct();
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        $index = 0;
        $texttemplates = [];

        /**
         * Replace content to TEMPLATE^N
         */
        $document = new Document();
        $document->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);
        foreach ($document->findXPath('//*') as $node) {
            $this->replaceTemplateNodeList($node->childNodes, $texttemplates, $index);
        }

        $content = $document->getHtml();

        /**
         * create nonReplyIndexes for return non reply index in template back
         */
        $nonReplyIndexes = [];
        foreach ($texttemplates as $key => $value) {
            $nonReplyIndexes[$key] = $key;
        }

        /**
         * make synonims
         */
        $syn = $this->synonimize($content, $texttemplates);

        /**
         * Return TEMPLATE^N to synonimizer content
         */
        $ex = preg_split('/TEMPLATE\^/', $syn);
        foreach ($ex as $e) {
            $exp = explode('=', $e);
            $index = (int) $exp[0];
            if (count($exp) > 2) {
                unset($exp[0]);
                $txt = trim(implode('=', $exp));
            } elseif (count($exp) == 2) {
                $txt = trim($exp[1]);
            } else {
                $txt = '';
            }
            $content = str_replace($this->getTemplate($index), $txt, $content);
            unset($nonReplyIndexes[$index]);
        }

        /**
         * Return nonReplyIndex if exists to original content
         */
        foreach ($nonReplyIndexes as $key => $index) {
            $content = str_replace($this->getTemplate($index), $texttemplates[$index], $content);
        }

        return $content;
    }

    /**
     * @param string $text
     * @param array $texttemplates
     * @return string|void
     * @throws \Exception
     */
    protected function synonimize(string $text, array $texttemplates)
    {
        // make synonims
        $synonims = [];
        $synonimstext = '';
        foreach ($texttemplates as $index => $template) {
            if (!empty(trim($template))) {
                $synonims[$index] = $template;
                $synonimstext .= $this->getTemplate($index)."=\"".$template."\"".PHP_EOL;
            }
        }
        return $this->synonimizer->synonimize($synonimstext);
    }

    /**
     * @param $nodes
     * @param array $texttemplates
     * @param int $index
     * @return void
     */
    protected function replaceTemplateNodeList($nodes, array &$texttemplates, int &$index)
    {
        foreach ($nodes as $node) {
            if ($node instanceof \DOMNodeList) {
                $this->replaceTemplateNodeList($node, $texttemplates, $index);
            } elseif ($node instanceof \DOMText) {
                $this->replaceNode($node, $texttemplates, $index);
            }
            $index++;
        }
    }

    /**
     * @param \DomText $node
     * @param array $texttemplates
     * @param int $index
     * @return void
     */
    protected function replaceNode(\DomText &$node, array &$texttemplates, int &$index)
    {
        $texttemplates[$index] = $node->nodeValue;
        $node->nodeValue = $this->getTemplate($index);
    }

    /**
     * @param int $index
     * @return string
     */
    protected function getTemplate(int $index)
    {
        return 'TEMPLATE^'.$index;
    }

}
