<?php

namespace App\Library\PRCY;

use DOMWrap\Document;
use DOMWrap\Element;
use DOMWrap\NodeList;
use App\Library\HtmlDomParser\HtmlParser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\String\ByteString;
use unique\proxyswitcher\Transport;
use unique\proxyswitcher\ArrayProxyList;
use Symfony\Component\DomCrawler\Crawler;

use function Symfony\Component\String\u;
use function Symfony\Component\String\b;
use function Symfony\Component\String\s;

class PrCyParser
{

    /**
     * @var string
     */
    protected string $url = 'https://a.pr-cy.ru/';

    /**
     * @var Document
     */
    protected $parser;

    /**
     * @var array
     */
    protected $proxies = [];

    /**
     * @var bool
     */
    protected $use_proxy = false;

    /**
     * @var int
     */
    protected $default_value_index = 4;

    /**
     * @param array $proxies
     * @param $use_proxy
     */
    public function __construct(array $proxies = [], $use_proxy = null)
    {
        // set proxies
        if (!empty($proxies) && is_null($use_proxy)) {
            $this->setProxies($proxies, true);
        } else {
            $this->setProxies($proxies, is_bool($use_proxy) ? $use_proxy : false);
        }

        // set parser
        $this->parser = new HTMLParser();
        $this->parser->setProxies($proxies);
    }

    /**
     * @param string $domain
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function parse(string $domain)
    {
        $domain = $this->clear_domain_name($domain);
        if (empty($domain)) {
            return null;
        }

        $content = $this->getDOMDocument($this->get_url_for_parsed_domain($domain));
        $data = $this->getReduxData($this->parser->getHtml());

        return $data ? json_decode($data) : null;
    }

    /**
     * @return array|null
     */
    public function check_backlinks(string $domain)
    {
        $domain = $this->clear_domain_name($domain);
        if (empty($domain)) {
            return null;
        }

        $item = [];

        $url = 'https://en.seokicks.de/backlinks/' . $this->clear_domain_name($domain);
        $this->parser->extractDocument($url);

        foreach ($this->parser->dom()->find('.form_compare table tr') as $node) {
            $name = $node->find('td')->eq(0) ? str_replace(['• ',':'], ['',''], $node->find('td')->eq(0)->getHtml()) : null;
            $count = $node->find('td')->eq(1) ? str_replace(['.'], [''], $node->find('td')->eq(1)->getHtml()) : null;
            if ($name && $count) {
                $item[$name] = $count;
            }
        }

        return $item;
    }

    public function translate(string $content)
    {
        $url = 'https://translate.yandex.ru/?lang=ru-en&text=%D0%AF%D0%BD%D0%B4%D0%B5%D0%BA%D1%81.%D0%9F%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%D1%87%D0%B8%D0%BA%C2%A0%E2%80%94%20%D1%81%D0%B8%D0%BD%D1%85%D1%80%D0%BE%D0%BD%D0%BD%D1%8B%D0%B9%20%D0%BF%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%20%D0%B4%D0%BB%D1%8F%20100%20%D1%8F%D0%B7%D1%8B%D0%BA%D0%BE%D0%B2%2C%20%D0%BF%D0%BE%D0%B4%D1%81%D0%BA%D0%B0%D0%B7%D0%BA%D0%B8%20%D0%BF%D1%80%D0%B8%20%D0%BD%D0%B0%D0%B1%D0%BE%D1%80%D0%B5%2C%20%D1%81%D0%BB%D0%BE%D0%B2%D0%B0%D1%80%D1%8C%20%D1%81%20%D1%82%D1%80%D0%B0%D0%BD%D1%81%D0%BA%D1%80%D0%B8%D0%BF%D1%86%D0%B8%D0%B5%D0%B9%2C%20%D0%BF%D1%80%D0%BE%D0%B8%D0%B7%D0%BD%D0%BE%D1%88%D0%B5%D0%BD%D0%B8%D0%B5%D0%BC%20%D0%B8%20%D0%BF%D1%80%D0%B8%D0%BC%D0%B5%D1%80%D0%B0%D0%BC%D0%B8%20%D1%83%D0%BF%D0%BE%D1%82%D1%80%D0%B5%D0%B1%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F%20%D1%81%D0%BB%D0%BE%D0%B2%2C%20%D0%B0%20%D1%82%D0%B0%D0%BA%D0%B6%D0%B5%20%D0%BC%D0%BD%D0%BE%D0%B3%D0%BE%D0%B5%20%D0%B4%D1%80%D1%83%D0%B3%D0%BE%D0%B5.%20%D0%A1%D0%B0%D0%BC%D1%8B%D0%B5%20%D0%BF%D0%BE%D0%BF%D1%83%2';
        $content = 'Используя эту консоль, вы можете подвергнуться атаке Self-XSS, что позволит злоумышленникам совершать действия от вашего имени и получать доступ к вашим данным.';

        $browser = new HttpBrowser(HttpClient::create([
            'max_redirects' => 7,
        ]));

        $parameters = [
            "text" => "Яндекс.Переводчик — синхронный перевод для 100 языков, подсказки при наборе, словарь с транскрипцией, произношением и примерами употребления слов, а также многое другое. Самые попу",
            "options" => 4,
        ];
        $crawler = $browser->request('POST', $url, $parameters);
        $cnt = $browser->getResponse()->getContent();
//        $cnt = $this->sanitize_tag($cnt, 'script');
        echo($cnt); die();
    }

    /**
     * @return void
     */
    protected function check_backlinks_old2()
    {
        $url = 'https://smallseotools.com/backlink-checker/';

        $browser = new HttpBrowser(HttpClient::create([
            'max_redirects' => 7,
        ]));
        $crawler = $browser->request('GET', $url);

        /**
         * Set Cookie for request
         */
        $cookieJar = $browser->getCookieJar();
        $cookie = new Cookie('submission', 'done', strtotime('+1 day'), '/', 'smallseotools.com');
        $cookieJar->set($cookie);


        $form = $crawler->filter('#backlink-checker-form')->form();
        $form_values = $form->getValues();

//        dump($cookieJar, $form_values);die();
        $parameters = [
//            'body' => [
                '_token' => $form_values['_token'],
                'url' => 'https://sailfishsportfishingcr.com/',
                'g-recaptcha-response' => true,
                'backlink_checker' => 'Get Backlinks',
//            ],
        ];

        $cc = '';
        try {
            //$cc = $this->parser->post($url, $parameters);
            $browser = new HttpBrowser(HttpClient::create(['max_redirects' => 7]), null, $cookieJar);
            $cc = $browser->request('POST', $url, $parameters);
        } catch (\Exception $e) {

        }
        echo($browser->getResponse()->getContent());
        die();

        $crawler = $browser->submit($form);

        $browser->clickLink('Sign in');
        $browser->submitForm($button, $parameters);
        $openPullRequests = trim($browser->clickLink('Pull requests')->filter(
            '.table-list-header-toggle a:nth-child(1)'
        )->text());


        echo($dom->getHtml());
    }

    /**
     * @param \DOMDocument $dom
     * @param string $str
     * @return \DOMNode|false
     */
    public function createElementFromHTML(\DOMDocument $dom, string $str)
    {
        libxml_use_internal_errors(true);
        $d = new \DOMDocument();
        $d->loadHTML($str);
        libxml_clear_errors();
        return $dom->importNode($d->documentElement,true);
    }

    /**
     * @param string $content
     * @return void
     */
    protected function getReduxData(string $content)
    {
        preg_match_all('/<script(.*?)>(.*?)window.REDUX_DATA(.*?)<\/script>/s', $content, $matches);
        if (! isset($matches[0][0])) {
            return '{}';
        }

        $data = $matches[0][0];

        if (str_contains($data, 'window.REDUX_DATA')) {
            return rtrim(trim(str_replace('window.REDUX_DATA = ', '', $data)), ";");
        } else {
            return '{}';
        }
    }

    /**
     * @return void
     */
    protected function check_backlinks_old3()
    {
        $url = 'https://smallseotools.com/backlink-checker/';
        $parameters = [
            '_token' => 'kgchrYivvfdoBi1suEpUyYNWBdzFZNspVLM559RC',
            'url' => 'https://sailfishsportfishingcr.com/',
            'g-recaptcha-response' => true,
            'backlink_checker' => 'Get Backlinks',
        ];
        $cookie = 'submission=done';
        $headers = [
            'Cookie: ' . $cookie,
        ];

        $parser = new HTMLParser();
        $parser->extractDocument($url);
        $parser->setMethod('POST');
        $parser->setBody($parameters);
        $parser->setHeaders($headers);
        $dom = $parser->dom();

        echo($dom->getHtml());
    }

    /**
     * @param string $content
     * @param string $tagName
     * @return void
     */
    public function sanitize_tag(string $content, string $tagName)
    {
        return preg_replace("#<$tagName(.*?)>(.*?)</$tagName>#is", '', $content);
    }

    /**
     * @param string $content
     * @return void
     */
    public function salitize_html(string $content)
    {
        $content = $this->sanitize_tag($content, 'form');
        $content = $this->sanitize_tag($content, 'footer');
        $content = $this->sanitize_tag($content, 'script');
        return $content;
    }

    /**
     * @return void
     */
    public function spec_project()
    {
        $c = file_get_contents('https://gksod.ru/');
        echo $this->salitize_html($c);

        $c = file_get_contents('https://www.emfy.com');
        echo $this->salitize_html($c);

        $c = file_get_contents('https://www.emfy.com/amocrm-widgets/google-sheets/');
        echo $this->salitize_html($c);

        $c = file_get_contents('https://treeography.com/');
        echo $this->salitize_html($c);

        $c = file_get_contents('https://www.emfy.com/amocrm-widgets/massovoe-redaktirovanie-poley/');
        echo $this->salitize_html($c);

        $c = file_get_contents('http://cgmnir.ru/');
        echo $this->salitize_html($c);

        die();
    }

    /**
     * @param \NodeList|\DOMElement|\DOMDocument $nodes
     * @return string
     */
    public function get_dom_html($nodes)
    {
        return implode(array_map([$nodes,"saveHTML"], iterator_to_array($nodes)));
    }

    /**
     * Get \DOMDocument element
     * @param string $html
     * @return \DOMDocument
     */
    protected function getDOMDocument(string $url)
    {
        $html = $this->parser->extractDocument($url, false);

        $content = $this->getReduxData($html);
        $this->parser->setHtml($content);

        return $this->parser->dom();
    }

    /**
     * @param string $domain
     * @return string
     */
    protected function get_url_for_parsed_domain(string $domain) : string
    {
        return $this->url . $this->clear_domain_name($domain);
    }

    /**
     * @return void
     */
    protected function clear_domain_name(string $domain)
    {
        return parse_url($domain, PHP_URL_PATH);
    }

    /**
     * Use proxy
     * @param bool $use_proxy
     */
    public function use_proxy(bool $use_proxy)
    {
        $this->use_proxy = $use_proxy;
    }

    /**
     * Set Proxy List (format proxy array of string 'IP:PORT')
     * @param array $proxies
     * @param bool $use_proxy
     * @return void
     */
    public function setProxies(array $proxies, bool $use_proxy = true)
    {
        $this->proxies = $proxies;
        $this->use_proxy = $use_proxy;
    }

    /**
     * Get Proxy List
     * @return array
     */
    public function getProxies(): array
    {
        return $this->proxies;
    }

    /**
     * @param HtmlParser $dom
     */
    public function setParser(HtmlParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return HtmlParser
     */
    public function getParser(): HtmlParser
    {
        return $this->parser;
    }

    /**
     * @return Document
     */
    public function dom(): Document
    {
        return $this->parser->dom();
    }


}
