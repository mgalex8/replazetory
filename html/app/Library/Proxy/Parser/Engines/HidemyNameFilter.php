<?php
namespace App\Library\Proxy\Parser\Engines;

use App\Library\Proxy\Parser\Interfaces\IProxyListParserFilter;

/**
 * Class HidemyNameFilter
 */
class HidemyNameFilter implements IProxyListParserFilter
{

    /**
     * @const string
     */
    const FILTER_PROXY_TYPE_HTTP    = 'h';
    const FILTER_PROXY_TYPE_HTTPS   = 's';
    const FILTER_PROXY_TYPE_SOCKS4  = '4';
    const FILTER_PROXY_TYPE_SOCKS5  = '5';

    /**
     * @const string
     */
    const FILTER_ANON_NONE          = '1';
    const FILTER_ANON_LOW           = '2';
    const FILTER_ANON_MIDDLE        = '3';
    const FILTER_ANON_HIGH          = '4';

    /**
     * @const string
     */
    const FILTER_COUNTRY_ARGENTINA              = 'AR'; // Argentina (4)
    const FILTER_COUNTRY_ARMENIA                = 'AM'; // Armenia (971)
    const FILTER_COUNTRY_AUSTRIA                = 'AT'; // Austria (1)
    const FILTER_COUNTRY_BANGLADESH             = 'BD'; // Bangladesh (1)
    const FILTER_COUNTRY_BELARUS                = 'BY'; // Belarus (1)
    const FILTER_COUNTRY_BELIZE                 = 'BZ'; // Belize (1658)
    const FILTER_COUNTRY_BRAZIL                 = 'BR'; // Brazil (7)
    const FILTER_COUNTRY_BULGARIA               = 'BG'; // Bulgaria (1)
    const FILTER_COUNTRY_CAMBODIA               = 'KH'; // Cambodia (1)
    const FILTER_COUNTRY_CANADA                 = 'CA'; // Canada (239)
    const FILTER_COUNTRY_CHILE                  = 'CL'; // Chile (2)
    const FILTER_COUNTRY_CHINA                  = 'CN'; // China (3)
    const FILTER_COUNTRY_COLOMBIA               = 'CO'; // Colombia (5)
    const FILTER_COUNTRY_COSTA_RICA             = 'CR'; // Costa Rica (1)
    const FILTER_COUNTRY_CURACAO                = 'CW'; // Curacao (973)
    const FILTER_COUNTRY_CYPRUS                 = 'CY'; // Cyprus (1723)
    const FILTER_COUNTRY_CZECH_REPUBLIC         = 'CZ'; // Czech Republic (2)
    const FILTER_COUNTRY_EGYPT                  = 'EG'; // Egypt (3)
    const FILTER_COUNTRY_FINLAND                = 'FI'; // Finland (1)
    const FILTER_COUNTRY_FRANCE                 = 'FR'; // France (115)
    const FILTER_COUNTRY_GERMANY                = 'DE'; // Germany (294)
    const FILTER_COUNTRY_GHANA                  = 'GH'; // Ghana (1)
    const FILTER_COUNTRY_HONG_KONG              = 'HK'; // Hong Kong (5)
    const FILTER_COUNTRY_HUNGARY                = 'HU'; // Hungary (1)
    const FILTER_COUNTRY_INDIA                  = 'IN'; // India (9)
    const FILTER_COUNTRY_INDONESIA              = 'ID'; // Indonesia (10)
    const FILTER_COUNTRY_IRAN                   = 'IR'; // Iran (2)
    const FILTER_COUNTRY_ISRAEL                 = 'IL'; // Israel (1)
    const FILTER_COUNTRY_JAPAN                  = 'JP'; // Japan (9)
    const FILTER_COUNTRY_KAZAKSTAN              = 'KZ'; // Kazakhstan (243)
    const FILTER_COUNTRY_KOREA                  = 'KR'; // Korea (2)
    const FILTER_COUNTRY_KYRGYZSTAN             = 'KG'; // Kyrgyzstan (1)
    const FILTER_COUNTRY_MEXICO                 = 'MX'; // Mexico (2)
    const FILTER_COUNTRY_MYANMAR                = 'MM'; // Myanmar (1)
    const FILTER_COUNTRY_NETHERLANDS            = 'NL'; // Netherlands (223)
    const FILTER_COUNTRY_PAKISTAN               = 'PK'; // Pakistan (1)
    const FILTER_COUNTRY_PANAMA                 = 'PA'; // Panama (2)
    const FILTER_COUNTRY_PERU                   = 'PE'; // Peru (1)
    const FILTER_COUNTRY_PHILIPPINES            = 'PH'; // Philippines (1)
    const FILTER_COUNTRY_ROMANIA                = 'RO'; // Romania (497)
    const FILTER_COUNTRY_RUSSIA                 = 'RU'; // Russian Federation (16)
    const FILTER_COUNTRY_RWANDA                 = 'RW'; // Rwanda (1)
    const FILTER_COUNTRY_SINGAPORE              = 'SG'; // Singapore (5)
    const FILTER_COUNTRY_SLOVAKIA               = 'SK'; // Slovakia (1)
    const FILTER_COUNTRY_SOUTH_AFRICA           = 'ZA'; // South Africa (1)
    const FILTER_COUNTRY_SPAIN                  = 'ES'; // Spain (178)
    const FILTER_COUNTRY_SWITZERLAND            = 'CH'; // Switzerland (2)
    const FILTER_COUNTRY_TAIWAN                 = 'TW'; // Taiwan (3)
    const FILTER_COUNTRY_THAILAND               = 'TH'; // Thailand (5)
    const FILTER_COUNTRY_TRINIDAD_AND_TOBAGO    = 'TT'; // Trinidad and Tobago (1)
    const FILTER_COUNTRY_UKRAINE                = 'UA'; // Ukraine (4)
    const FILTER_COUNTRY_UNITED_KINGDOM         = 'GB'; // United Kingdom (516)
    const FILTER_COUNTRY_UNITED_STATES          = 'US'; // United States (2106)
    const FILTER_COUNTRY_VIETNAM                = 'VN'; // Vietnam (17)
    const FILTER_COUNTRY_VIRGIN_ISLANDS         = 'VG'; // Virgin Islands, British (1237)

    protected $filter_countries = [
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AT' => 'Austria',
        'BD' => 'Bangladesh',
        'BY' => 'Belarus',
        'BZ' => 'Belize',
        'BR' => 'Brazil',
        'BG' => 'Bulgaria',
        'KH' => 'Cambodia',
        'CA' => 'Canada',
        'CL' => 'Chile',
        'CN' => 'China',
        'CO' => 'Colombia',
        'CR' => 'Costa Rica',
        'CW' => 'Curacao',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'EG' => 'Egypt',
        'FI' => 'Finland',
        'FR' => 'France',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IL' => 'Israel',
        'JP' => 'Japan',
        'KZ' => 'Kazakstan',
        'KR' => 'Korea',
        'KG' => 'Kyrgyzstan',
        'MX' => 'Mexico',
        'MM' => 'Myanmar',
        'NL' => 'Netherlands',
        'PK' => 'Pakistan',
        'PA' => 'Panama',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'ZA' => 'South Africa',
        'ES' => 'Spain',
        'CH' => 'Switzerland',
        'TW' => 'Taiwan',
        'TH' => 'Thailand',
        'TT' => 'Trinidad and Tobago',
        'UA' => 'Ukraine',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'VN' => 'Vietnam',
        'VG' => 'Virgin Islands, British',
    ];

    /**
     * @var string[]
     */
    protected $filter_countries_values = [
        'AR', 'AM', 'AT', 'BD', 'BY', 'BZ', 'BR', 'BG', 'KH', 'CA',
        'CL', 'CN', 'CO', 'CR', 'CW', 'CY', 'CZ', 'EG', 'FI', 'FR',
        'DE', 'GH', 'GT', 'HN', 'HK', 'HU', 'IN', 'ID', 'IR', 'IL',
        'JP', 'KZ', 'KR', 'KG', 'MX', 'MM', 'NL', 'PK', 'PA', 'PE',
        'PL', 'RO', 'RU', 'RW', 'SG', 'SK', 'ZA', 'ES', 'CH', 'TW',
        'TH', 'TT', 'TR', 'UA', 'GB', 'US', 'VN', 'VG'
    ];

    /**
     * @var string[]
     */
    protected $filter_type_values = ['h', 's', '4', '5'];

    /**
     * @var string[]
     */
    protected $filter_anon_values = ['1', '2', '3', '4'];

    /**
     * @var string
     */
    protected $parameters;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $depth;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = [];
        $this->page = 1;
    }

    /**
     * Get Filter Url As string
     * @return string
     */
    public function getUrlString() : string
    {
        return implode('&', $this->parameters);
    }

    /**
     * Check filter is empty
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->parameters) ? true : false;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isset(string $key)
    {
        return isset($this->parameters[$key]);
    }

    /**
     * Set filter parameters
     * @param array $args
     * @return void
     */
    public function setParameters(array $args = [])
    {
        // set anonymous filter
        if (isset($args['mixtime']) && is_numeric($args['mixtime'])) {
            $this->parameters['maxtime'] = 'maxtime='.$args['mixtime'];
        } else {
            unset($this->parameters['maxtime']);
        }

        // set anonymous filter
        if (isset($args['countries']) && is_array($args['countries']) && count( array_intersect($args['types'], $this->filter_type_values) ) > 0) {
            $this->parameters['country'] = 'country=' . implode('', array_intersect($args['countries'], $this->filter_countries_values));
        } else {
            unset($this->parameters['country']);
        }

        // set anonymous filter
        if (isset($args['types']) && is_array($args['types']) && count( array_intersect($args['types'], $this->filter_type_values) ) > 0) {
            $this->parameters['type'] = 'type=' . implode('', array_intersect($args['types'], $this->filter_type_values));
        } else {
            unset($this->parameters['type']);
        }

        // set anonymous filter
        if (isset($args['anon']) && is_array($args['anon']) && count( array_intersect($args['anon'], $this->filter_anon_values) ) > 0) {
            $this->parameters['anon'] = 'anon=' . implode('', array_intersect($args['anon'], $this->filter_anon_values));
        } else {
            unset($this->parameters['anon']);
        }
    }

    /**
     * Get filter parameters
     * @return array
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Set page paginate
     * @param int $page
     * @return void
     */
    public function setPage(int $page = 1)
    {
        if ($page == 1) {
            unset($this->parameters['start']);
        } else {
            $start = ($page - 1) * 64;

            $this->parameters['start'] = 'start=' . $start;
        }

        $this->page = $page;
    }

    /**
     * Get page paginate
     * @return int
     */
    public function getPage() : int
    {
        return $this->page;
    }

    /**
     * @param string $country_name
     * @return string
     */
    public function getCCV(string $country_name)
    {
        return array_search($country_name, $this->filter_countries) ?: '';
    }

    /**
     * Return filter as string
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUrlString();
    }
}
