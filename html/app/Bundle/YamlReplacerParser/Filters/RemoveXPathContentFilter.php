<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;
use DOMWrap\Document;

/**
 * Class TrimContentFilter
 */
class RemoveXPathContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'remove_xpath';

    /**
     * @return void
     */
    public function configure() : void
    {
        $this->setOption('xpath', '', 'is_string');
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        $document = new Document();
        $document->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);
        foreach ($document->findXPath($options['xpath']) as $node) {
            $node->parentNode->removeChild($node);
        }
        return preg_replace('/<\?xml\sencoding\=\"UTF\-8\"><domwrap><\/domwrap>/', '', $document->getHtml());
    }

}
