<?php

namespace App\Library\PRCY;

use DOMWrap\Document;
use DOMWrap\Element;
use DOMWrap\NodeList;
use App\Library\HtmlDomParser\HtmlParser;
use Symfony\Component\CssSelector\CssSelectorConverter;
use unique\proxyswitcher\Transport;
use unique\proxyswitcher\ArrayProxyList;
use Symfony\Component\DomCrawler\Crawler;

class PrCyParserHtml
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
     * @param Element $div
     * @return array
     */
    public function get_parameters()
    {
        return [
//            ['id' => 'parameters_ya_x2',                'name' => 'Яндекс ИКС',                 'xpath' => "XPATH://div[@id='app']/div[9]/div[28]/div[29]/div[34]/div[35]/div[37]/ul[2]/li[8]", 'has' => true],
//
//
            ['id' => 'parameters_ya_x',                 'name' => 'Яндекс ИКС',                 'xpath' => "XPATH://*[text()='Яндекс ИКС']/following-sibling::div[1]"],
            ['id' => 'parameters_yandex_reviews',       'name' => 'Яндекс Отзывы',              'xpath' => "XPATH://*[text()='Яндекс Отзывы']/following-sibling::div[1]"],
            ['id' => 'parameters_yandex_index',         'name' => 'Yandex Индексация',          'xpath' => "XPATH://*[text()='Yandex Индексация']/following-sibling::div[1]"],
            ['id' => 'parameters_google_index',         'name' => 'Google Индексация',          'xpath' => "XPATH://*[text()='Google Индексация']/following-sibling::div[1]"],
            ['id' => 'parameters_google_safe_review',   'name' => 'Google безопасный просмотр', 'xpath' => "XPATH://*[text()='Google безопасный просмотр']/following-sibling::div[1]"],
            ['id' => 'parameters_yandex_virus',         'name' => 'Яндекс вирусы',              'xpath' => "XPATH://*[text()='Яндекс вирусы']/following-sibling::div[1]"],
            ['id' => 'parameters_filter_ags',           'name' => 'Фильтр АГС',                 'xpath' => "XPATH://*[text()='Фильтр АГС']/following-sibling::div[1]"],

            ['id' => 'parameters_yandex_zn_user_prefs', 'name' => 'Выбор пользователей',    'xpath' => "XPATH://*[text()='Яндекс Знаки']/following-sibling::div[1]/div[1]/*[contains(text(),'Выбор пользователей')]"],
            ['id' => 'parameters_yandex_zn_popular',    'name' => 'Популярный сайт',        'xpath' => "XPATH://*[text()='Яндекс Знаки']/following-sibling::div[1]/div[1]/*[contains(text(),'Популярный сайт')]"],
            [
                'id' => 'parameters_yandex_zn_safes',
                'name' => 'Защищенное соединение',
                'xpath' => "XPATH://*[text()='Яндекс Знаки']/following-sibling::div[1]/div[1]/*[contains(text(),'Защищенное соединение')]",
                'value' => "XPATH://*[text()='Яндекс Знаки']/following-sibling::div[1]/div[1]/*[contains(text(),'Защищенное соединение')]/svg/path[@fill='#3169F0'] -> result('Y'|'N')",
            ],
            [
                'id' => 'parameters_yandex_zn_turbo',
                'name' => 'Турбо-страницы',
                'value' => "XPATH://*[text()='Яндекс Знаки']/following-sibling::div[1]/div[1]/*[contains(text(),'Защищенное соединение')]/svg/path[@fill='#3169F0'] -> result('Y'|'N')",
                'has' => function (Element $div): bool {
                    $nodes = $div->findXPath("//*[text()='Яндекс Знаки']/following-sibling::div[1]/div[1]/*[contains(text(),'Турбо-страницы')]");
                    return $nodes->count() > 0;
                },
                'html' => function (Element $div): string {
                    $nodes = $div->findXPath("//*[text()='Яндекс Знаки']/following-sibling::div[1]/div[1]/*[contains(text(),'Турбо-страницы')]");
                    return $nodes->count() > 0 ? $nodes->eq(0)->html() : '';
                },
            ],

            // аккаунты в соцсетях
//            ['id' => 'inseo_vk', 'name' => 'VK.com', 'xpath' => "XPATH://div[child::*[@id='inSeo']]//following-sibling::*[contains(text(),'VK.com')]"],
//            ['id' => 'inseo_fb', 'name' => 'FB.com', 'value_index' => 3],
//            ['id' => 'inseo_twitter', 'name' => 'Twitter', 'value_index' => 3],
//            [
//                'id' => 'inseo_telegram',
//                'name' => 'Telegram',
//                'xpath' => "XPATH://div[child::*[@id='inSeo']]//following-sibling::*[contains(text(),'Telegram')]",
//                'html' => new XPathFounder("//div[child::*[@id='inSeo']]//following-sibling::a[contains(@href,'telegram')]"),
//                'value' => new XPathFounder("//div[child::*[@id='inSeo']]//following-sibling::a[contains(@href,'telegram')]"),
//            ],
//            ['id' => 'inseo_facebook', 'name' => 'Facebook', 'value_index' => 3],
//            ['id' => 'inseo_youtube', 'name' => 'youtube.com', 'value_index' => 3],

            // посещаемость
            ['id' => 'traffic_users_mouth', 'name' => 'Посетители/мес',
                'xpath' => "XPATH://*[text()='Посетители']/div[@title]",
//                'has' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Посетители']/div[@title]~",
//                'html' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Посетители']~ -> html()",
                'value' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Посетители']/div[@title]~ -> attr('title')",
            ],
            ['id' => 'traffic_users_day', 'name' => 'Посетители/мес',
                'xpath' => "XPATH://*[text()='Посетители']/span[@title]",
//                'has' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Посетители']/span[@title]~",
//                'html' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Посетители']/span[@title]~",
                'value' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Посетители']/span[@title]~ -> attr('title')",
            ],

            ['id' => 'traffic_reviews_mouth', 'name' => 'Просмотры/мес',
                'has' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Просмотры']/div[@title]~",
                'html' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Просмотры']/div[@title]~ -> first()",
                'value' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Просмотры']/div[@title]~ -> attr('title')",
            ],
            ['id' => 'traffic_reviews_day', 'name' => 'Просмотры/день',
                'has' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Просмотры']/span[@title]~",
                'html' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Просмотры']/span[@title]~ -> first()",
                'value' => "XPATH://div[child::*[@id='traffic']] -> child ~//*[text()='Просмотры']/span[@title]~ -> attr('title')",
            ],
//
//            // переходы
//            ['id' => 'traffic_source_base', 'name' => 'Прямые заходы'],
//            ['id' => 'traffic_source_searchengines', 'name' => 'Поисковые системы'],
//            ['id' => 'traffic_source_email', 'name' => 'Почтовые рассылки'],
//            ['id' => 'traffic_source_social', 'name' => 'Социальные сети'],
//
//            // социальная активность
//            ['id' => 'traffic_source_youtube', 'name' => 'youtube.com'],
//            ['id' => 'traffic_source_vk', 'name' => 'vk.com'],
//            ['id' => 'traffic_source_whatsapp', 'name' => 'web.whatsapp.com'],
//            ['id' => 'traffic_source_other', 'name' => 'Другое'],
//
//            ['id' => 'traffic_paytraf', 'name' => 'Платный трафик'],

            ['id' => 'traffic_rating_world', 'name' => 'Место в мире', 'xpath' => "XPATH://*[text()='Место в мире']/preceding::div[2]"],
//            ['id' => 'traffic_rating_country', 'name' => 'Место в стране', 'xpath' => "XPATH://*[text()='Место в стране']/preceding::div[2]"],
//            ['id' => 'traffic_user_geo', 'name' => 'География посетителей', 'xpath' => "XPATH://*[text()='География посетителей']/following-sibling::table"],
//            ['id' => 'traffic_analogy_sites', 'name' => 'Похожие сайты', 'xpath' => "XPATH://*[text()='Похожие сайты']//following-siblings::*[0]/table"],
//            ['id' => 'traffic_category', 'name' => 'Категории интересов'],
//            ['id' => 'traffic_themes', 'name' => 'Темы интересов'],
//            ['id' => 'traffic_interest', 'name' => 'Также интересуются'],
//            ['id' => 'traffic_counters', 'name' => 'Установленные системы статистики'],
//
//
//            // ссылки
            [
                'id' => 'backlinks_links',
                'name' => 'Обратные ссылки',
                'xpath' => "XPATH://*[text()='Обратные ссылки']/preceding::div[1]",
                'value' => "XPATH://*[text()='Обратные ссылки']/preceding::div[1]/span[1] -> attr('title')"
            ],
            [
                'id' => 'backlinks_domains',
                'name' => 'Ссылается доменов',
                'xpath' => "XPATH://*[text()='Ссылается доменов']/preceding::div[1]",
                'value' => "XPATH://*[text()='Ссылается доменов']/preceding::div[1]/span[1] -> attr('title')"
            ],
            [
                'id' => 'backlinks_site_links',
                'name' => 'Исходящие ссылки с сайта',
                'xpath' => "XPATH://*[text()='Исходящие ссылки с сайта']/following-sibling::div[1]//span",
            ],
//            ['id' => 'backlinks_natural_links', 'name' => 'Естественность ссылок'],
            ['id' => 'backlinks_new_links', 'name' => 'Новые ссылки на сайт', 'xpath' => "XPATH://*[text()='Новые ссылки на сайт']//parent::*[0]"],

//
//            // keywords
//            ['id' => 'keywords_yandex', 'name' => 'Поисковые запросы'],
//            ['id' => 'keywords_yandex_1', 'name' => 'Яндекс'],
//            ['id' => 'keywords_yandex_2', 'name' => 'Ключевые слова'],
//
//            // техническое состояние сайта
//            ['id' => 'technical_sitemaps', 'name' => 'Наличие Sitemap'],
//            ['id' => 'technical_page404', 'name' => 'Код ответа страницы 404'],

        /**
            // сервер
//            ['id' => 'technical_server', 'name' => 'Местоположение сервера'],          // ok
//            ['id' => 'technical_datacenter', 'name' => 'Датацентр'],                    // ok
         */
        ];
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

        $result = [];

        /**
         * Set parameter array and processing
         */
        $parameter_array = $this->get_parameters();

        $dom = $this->getDOMDocument('https://a.pr-cy.ru/www.reg.ru/');
        $xp = new \DOMXpath($dom);

//        $time = round(microtime(true) * 1000);

        // $selector = '//#app/div[not(header)][1]/div[3]/div';
        $selector = "//div[1]";

        foreach ($xp->query($selector) as $div) {
            foreach ($parameter_array as $key => $param) {
                try {
                    $processing = (new ParameterProcessingElement())->processingParameter($param, $div);
                } catch (\Exception $e) {
                    dump($param);
                    throw $e;
                }

                $result[ $processing['id'] ] = $processing;
                if ($processing['has']) {
                    unset($parameter_array[$key]);
                }
            }
            break;
        }

//        $time = round(microtime(true) * 1000) - $time;
//        dump($time);

        return $result;
    }

    /**
     * Get \DOMDocument element
     * @param string $html
     * @return \DOMDocument
     */
    protected function getDOMDocument(string $url)
    {
        try {
            $this->parser->extractDocument($url);
            $dom = $this->parser->dom();
        } catch(\Exception $e) {
            $html = file_get_contents($url);
            $dom = new Document();
            $dom->setHtml($html);
        }

        return $dom;
    }

    /**
     * @param string $domain
     * @return string
     */
    protected function get_url_for_parsed_domain(string $domain) : string
    {
        return $this->url . $domain;
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
