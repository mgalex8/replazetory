<?php
namespace App\Library\Synonimizer;

use App\Library\Synonimizer\Filters\IContentFilter;

class Synonimizer
{

    /**
     * @var string
     */
    protected $url = 'https://raskruty.ru/tools/synonymizer/run.php';

    /**
     * @var string|null
     */
    protected $curl_error = null;

    /**
     * @var int|null
     */
    protected $curl_errno = null;

    /**
     * @var array
     */
    protected $filters;

    /**
     * Synonimize text
     * @param string $text
     * @param string $dict
     * @param string $stopwords
     * @return void
     */
    public function synonimize(string $text, string $dict = 'base', string $stopwords = '')
    {
        $result = $this->post($text, $dict, $stopwords);
        if ($this->getErrorCode()) {
            throw new \Exception(sprintf('Curl error %s: %s', $this->getErrorCode(), $this->getError()));
        }

        if (! preg_match('/\|\|\|(.*?)\|\|\|(.*?)\|\|\|/', $result, $matches)) {
            return '';
        } elseif (! isset($matches[2])) {
            return '';
        }

        $synonims = $matches[2];

        if ($this->filters) {
            foreach ($this->filters as $filter) {
                $synonims = $filter->filter($synonims);
            }
        }

        return $synonims;
    }

    /**
     * @param string $text
     * @return void
     */
    public function post(string $text, string $dict = 'base', string $stopwords = '')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "text=$text&dict=$dict&stopwords=$stopwords");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        $this->curl_errno = curl_errno($ch);
        $this->curl_error = curl_error($ch);

        curl_close($ch);

        return $server_output;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->curl_error;
    }

    /**
     * @return int|null
     */
    public function getErrorCode()
    {
        return $this->curl_errno;
    }

    /**
     * @param IContentFilter $filter
     * @return void
     */
    public function setFilter(IContentFilter $filter)
    {
        $this->filters[$filter->getName()] = $filter;
    }

}