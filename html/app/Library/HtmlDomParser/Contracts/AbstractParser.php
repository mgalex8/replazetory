<?php
namespace App\Library\HtmlDomParser\Contracts;

use App\Library\HtmlDomParser\Exceptions\HttpReqeustParseException;
use WPDesk\PluginBuilder\Storage\Exception\ClassAlreadyExists;

abstract class AbstractParser
{

    /**
     * @var \WP_Http
     */
    protected $http;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $cache_group;

    /**
     * @var int
     */
    protected $cache_ttl;

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var array
     */
    protected $user_agent = [];

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var array
     */
    protected $proxies = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array|string
     */
    protected $body;


    /**
     * @param string $url
     * @param AbstractOptions|null $options
     */
    public function __construct(string $url = '', ?AbstractOptions $options = null)
    {
        // set url and extract document
        if (! empty($url)) {
            $this->setUrl($url);
            $this->extractDocument($url);
        }
    }

    public function response(string $url, array $options = [], $method = '', $asJson = false, $payload = true)
    {
        $method = $method ?: $this->method;
        return file_get_contents($url);
    }

    /**
     * @param string $url
     * @param array $options
     * @return false|string|void
     */
    public function get(string $url, array $options = [])
    {
        try {
            return $this->response($url, $options, 'GET');
        } catch(\Exception $e) {
            return file_get_contents($url);
        }
    }

    /**
     * @param string $url
     * @param array $options
     * @return false|string|void
     */
    public function post(string $url, array $options = [])
    {
        return $this->response($url, $options, 'POST');
    }

    /**
     * Remote response
     * @param string $url
     * @param array $data
     * @param string $method
     * @return void
     */
    public function getFromHttp(string $url, array $options = [], $method = 'GET', $asJson = false, $payload = true)
    {
        $http = $this->http();

        $args = [];

        if (! empty($this->body)) {
            $args['body'] = $this->body;
        }
        if (! empty($this->headers)) {
            $args['headers'] = $this->getRequestHeaders();
        }
        if (! empty($this->proxies)) {
            $args['proxy'] = $this->proxies;
        }
        if (! empty($this->user_agent)) {
            $args['user-agent'] = $this->user_agent;
        }

        $args = array_merge($options, $args);

        // request
        switch (strtoupper($method)) {
            case 'GET':
                $response = $http->get($url, $args);
                break;
            case 'POST':
            default:
                $response = $http->post($url, $args);
        }

        $this->checkErrors($response);

        return isset($response['body']) ? $response['body'] : '';
    }

    /**
     * @param $response
     * @return void
     * @throws \App\Library\HtmlDomParser\Exceptions\HttpReqeustParseException
     */
    protected function checkErrors($response)
    {
        $exception = null;

        if ($response instanceof \WP_Error) {
            $code = $response->get_error_code() ?? 500;
            $exception = new HttpReqeustParseException(sprintf('%s; url: %s', implode('; ', $response->get_error_messages()), $this->url), (int) $code);
        }
        elseif (! isset($response['body']) || ! isset($response['response']['code']) || $response['response']['code'] != 200) {
            $code = $response['response']['code'] ?? 500;
            $exception = new HttpReqeustParseException(sprintf('Http Request return status %s, url: %s', $code, $this->url), (int) $code);
        }

        if ($exception) {
            $exception->setResponse($response);
            throw $exception;
        }
    }

    /**
     * Get random User Agent
     * @return string
     */
    protected function getRandomUserAgent()
    {
        $agents = array_merge( $this->user_agent, [
            'My John Doe Agent 1.0',
            'Mozilla/5.6 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2',
            'Mozilla/5.0.34 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
            'Mozilla/1.22 (compatible; MSIE 10.0; Windows 3.1.1)',
            'Mozilla/4.0.9 (Windows NT 6.3; Trident/7.0; rv:11.0.4) like Gecko',
            'Opera/21.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/8504933.21',
        ]);

        $chose = rand(0, count($agents) - 1);
        return $agents[$chose];
    }

    /**
     * Get response headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set request headers
     * @param array headers
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Set proxy list
     * @param array proxies
     * @return void
     */
    public function setProxies(array $proxies)
    {
        $this->proxies = $proxies;
    }

    /**
     * @param array|string $body
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param array $user_agent
     * @return void
     */
    public function setUserAgent(array $user_agent)
    {
        $this->user_agent = $user_agent;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function url() : string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return void
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get WP_Http object
     * @return WP_Http
     */
    public function http()
    {
        if ( is_null( $this->http ) ) {
            $this->http = _wp_http_get_object();
        }
        return $this->http;
    }

    /**
     * @param array $tags
     * @param array $keys
     */
    public function clearCache() : void
    {
        wp_cache_flush();
    }

    /**
     * Get cache key
     * @param string $value
     * @return string
     */
    protected function cacheKey(string $value) : string
    {
        return $this->cache_group . '.' . md5($value);
    }

    /**
     * Extract document with cache and set object options
     * abstract function
     * @param string $url
     * @param bool $set_html
     * @return void
     */
    protected abstract function extractDocument(string $url = '', bool $set_html = true) : string;

}
