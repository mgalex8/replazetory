<?php

namespace App\Library\RegRu\Filters;

/**
 * filter_expiring=5
 * filter_expiring=5
 * filter_dnames=dom.ru
 * filter_symbols_from=11
 * filter_symbols_to=22
 * filter_withoutdash=1
 * filter_withoutnumber=1
 * filter_withnumber=1
 * filter_zone_ru=1
 * filter_zone_rf=1
 * filter_zone_su=1
 * filter_tic_from=20
 * filter_tic_to=50
 * filter_pr_from=1
 * filter_pr_to=6
 * filter_current_bid_from=100
 * filter_current_bid_to=2900
 * filter_limit_bid_from=20
 * filter_limit_bid_to=5999
 * filter_bid_type=without_bids|
 * filter_creation_date_from=2022-07-06
 * filter_creation_date_to=2022-07-06
 * filter_free_date_from=2022-07-06
 * filter_free_date_to=2022-07-06
 * filter_registrar[]=101DOMAIN
 * filter_registrar[]=AAB
 * filter_registrar[]=ACTIVE
 * filter_registrar[]=ARDIS
 * filter_registrar[]=ATEX
 * filter_registrar[]=AXELNAME
 * filter_registrar[]=BEELINE
 * filter_registrar[]=BEGET
 * filter_registrar[]=CENTRALREG
 * filter_registrar[]=DOMAINER
 * filter_registrar[]=DOMAINS
 * filter_registrar[]=DOMAINSHOP
 * filter_registrar[]=DOMENUS
 * filter_registrar[]=DR
 * filter_registrar[]=DS
 * filter_registrar[]=FE
 * filter_registrar[]=FIREFOX
 * filter_registrar[]=FLEXBE
 * filter_registrar[]=KLONDIKE
 * filter_registrar[]=MASTERHOST
 * filter_registrar[]=MAXNAME
 * filter_registrar[]=MS
 * filter_registrar[]=NETFOX
 * filter_registrar[]=NETHOUSE
 * filter_registrar[]=OPENPROV
 * filter_registrar[]=PN
 * filter_registrar[]=R01
 * filter_registrar[]=RD
 * filter_registrar[]=REGRU
 * filter_registrar[]=REGTIME
 * filter_registrar[]=RELCOMHOST
 * filter_registrar[]=RFRU
 * filter_registrar[]=RT
 * filter_registrar[]=RUCENTER
 * filter_registrar[]=RUNET
 * filter_registrar[]=SALENAMES
 * filter_registrar[]=SHOP
 * filter_registrar[]=SPRINTNAMES
 * filter_registrar[]=TIMEWEB
 * filter_registrar[]=UNINIC
 * filter_registrar[]=WEBNAMES
 * filter_registrar[]=YU
 */
class RegRuDomainsFilter
{

    /**
     * Ставки
     * @const string
     */
    public const FILTER_BID_TYPE_NONE           = '';             // Неважно
    public const FILTER_BID_TYPE_BIDS           = 'bids';         // Со ставками
    public const FILTER_BID_TYPE_MY_BIDS        = 'my_bids';      // С моими ставками
    public const FILTER_BID_TYPE_WITHOUT_BIDS   = 'without_bids'; // Без ставок

    /**
     * Фильтр регистраторов
     * @const string
     */
    public const FILTER_REGISTRAR_101DOMAIN     = '101DOMAIN';
    public const FILTER_REGISTRAR_AAB           = 'AAB';
    public const FILTER_REGISTRAR_ARDIS         = 'ARDIS';
    public const FILTER_REGISTRAR_ATEX          = 'ATEX';
    public const FILTER_REGISTRAR_AXELNAME      = 'AXELNAME';
    public const FILTER_REGISTRAR_BEELINE       = 'BEELINE';
    public const FILTER_REGISTRAR_BEGET         = 'BEGET';
    public const FILTER_REGISTRAR_CENTRALREG    = 'CENTRALREG';
    public const FILTER_REGISTRAR_DOMAINER      = 'DOMAINER';
    public const FILTER_REGISTRAR_DOMAINS       = 'DOMAINS';
    public const FILTER_REGISTRAR_DOMAINSHOP    = 'DOMAINSHOP';
    public const FILTER_REGISTRAR_DOMENUS       = 'DOMENUS';
    public const FILTER_REGISTRAR_DR            = 'DR';
    public const FILTER_REGISTRAR_DS            = 'DS';
    public const FILTER_REGISTRAR_FE            = 'FE';
    public const FILTER_REGISTRAR_FIREFOX       = 'FIREFOX';
    public const FILTER_REGISTRAR_FLEXBE        = 'FLEXBE';
    public const FILTER_REGISTRAR_KLONDIKE      = 'KLONDIKE';
    public const FILTER_REGISTRAR_MASTERHOST    = 'MASTERHOST';
    public const FILTER_REGISTRAR_MAXNAME       = 'MAXNAME';
    public const FILTER_REGISTRAR_MS            = 'MS';
    public const FILTER_REGISTRAR_NETFOX        = 'NETFOX';
    public const FILTER_REGISTRAR_NETHOUSE      = 'NETHOUSE';
    public const FILTER_REGISTRAR_OPENPROV      = 'OPENPROV';
    public const FILTER_REGISTRAR_PN            = 'PN';
    public const FILTER_REGISTRAR_R01           = 'R01';
    public const FILTER_REGISTRAR_RD            = 'RD';
    public const FILTER_REGISTRAR_REGRU         = 'REGRU';
    public const FILTER_REGISTRAR_REGTIME       = 'REGTIME';
    public const FILTER_REGISTRAR_RELCOMHOST    = 'RELCOMHOST';
    public const FILTER_REGISTRAR_RFRU          = 'RFRU';
    public const FILTER_REGISTRAR_RT            = 'RT';
    public const FILTER_REGISTRAR_RUCENTER      = 'RUCENTER';
    public const FILTER_REGISTRAR_RUNET         = 'RUNET';
    public const FILTER_REGISTRAR_SALENAMES     = 'SALENAMES';
    public const FILTER_REGISTRAR_SHOP          = 'SHOP';
    public const FILTER_REGISTRAR_SPRINTNAMES   = 'SPRINTNAMES';
    public const FILTER_REGISTRAR_TIMEWEB       = 'TIMEWEB';
    public const FILTER_REGISTRAR_UNINIC        = 'UNINIC';
    public const FILTER_REGISTRAR_WEBNAMES      = 'WEBNAMES';
    public const FILTER_REGISTRAR_YU            = 'YU';

    /**
     * Фильтр параметр 'Освобождаются'
     *      ~ Значения: 1-7
     *
     * @var int
     */
    public int $filter_expiring = 0;

    /**
     * Фильтр параметр 'Домены'
     *      ~ Поиск по названию домена.
     *      ~ "*" заменяет группу символов,
     *      ~ фраза в кавычках обозначает строгий поиск,
     *      ~ несколько доменов разделяются пробелами
     *
     * @var string
     */
    public string $filter_dnames = '';

    /**
     * Фильтр параметр 'Длина от'
     *      ~ Количество символов в домене от
     *
     * @var int
     */
    public int $filter_symbols_from = 0;

    /**
     * Фильтр параметр 'Длина до'
     *      ~ Количество символов в домене до
     *
     * @var int
     */
    public int $filter_symbols_to = 0;

    /**
     * Фильтр параметр 'Без дефиса'
     *      ~ Фильтр названий доменов без дефиса
     *      ~ Значения: 0/1
     *
     * @var int
     */
    public int $filter_withoutdash = 0;

    /**
     * Фильтр параметр 'Без цифр'
     *      ~ Фильтр названий доменов без цифр
     *      ~ Значения: 0/1
     *
     * @var int
     */
    public int $filter_withoutnumber = 0;

    /**
     * Фильтр параметр 'Цифровык домены'
     *      ~ Фильтр названий доменов с цифрами
     *      ~ Значения: 0/1
     *
     * @var int
     */
    public int $filter_withnumber = 0;

    /**
     * Фильтр параметр 'Зона .RU'
     *      ~ Фильтр доменной доны .ru
     *      ~ Значения: 0/1
     *
     * @var int
     */
    public int $filter_zone_ru = 0;

    /**
     * Фильтр параметр 'Зона .РФ'
     *      ~ Фильтр доменной доны .рф
     *      ~ Значения: 0/1
     *
     * @var int
     */
    public int $filter_zone_rf = 0;

    /**
     * Фильтр параметр 'Зона .SU'
     *      ~ Фильтр доменной доны .su
     *      ~ Значения: 0/1
     *
     * @var int
     */
    public int $filter_zone_su = 0;

    /**
     * Фильтр параметр 'Яндекс ТИЦ от'
     *      ~ Значения: 0 - 99999999 или пусто
     *
     * @var int
     */
    public int $filter_tic_from = 0;

    /**
     * Фильтр параметр 'Яндекс ТИЦ до'
     *      ~ Значения: 0 - 99999999 или пусто
     *
     * @var int
     */
    public int $filter_tic_to = 0;

    /**
     * Фильтр параметр 'Google PR от'
     *      ~ Значения: 0 -10 или пусто
     *
     * @var int
     */
    public int $filter_pr_from = 0;

    /**
     * Фильтр параметр 'Google PR до'
     *      ~ Значения: 0 -10 или пусто
     *
     * @var int
     */
    public int $filter_pr_to = 0;

    /**
     * Фильтр параметр 'Текущая ставка цены от'
     *      ~ Значения: 0 - 99999999 или пусто
     *
     * @var int
     */
    public int $filter_current_bid_from = 0;

    /**
     * Фильтр параметр 'Текущая ставка цены до'
     *      ~ Значения: 0 - 99999999 или пусто
     *
     * @var int
     */
    public int $filter_current_bid_to = 0;

    /**
     * Фильтр параметр 'Предел ставки от'
     *      ~ Значения: 0 - 99999999 или пусто
     *
     * @var int
     */
    public int $filter_limit_bid_from = 0;

    /**
     * Фильтр параметр 'Предел ставки до'
     *      ~ Значения: 0 - 99999999 или пусто
     *
     * @var int
     */
    public int $filter_limit_bid_to = 0;

    /**
     * Фильтр параметр 'Ставки'
     *      ~ Фильтр типов ставок
     *      ~ Значения: bids / my_bids / without_bids / или пусто
     *      ~ См константы вида RegRuFilter::FILTER_BID_TYPE_..
     *
     * @var string
     */
    public string $filter_bid_type = '';

    /**
     * Фильтр параметр 'Дата регистрации с'
     *      ~ Дата вида 2022-08-19 или пусто
     *
     * @var string
     */
    public string $filter_creation_date_from = '';

    /**
     * Фильтр параметр 'Дата регистрации по'
     *      ~ Дата вида 2022-08-19 или пусто
     *
     * @var string
     */
    public string $filter_creation_date_to = '';

    /**
     * Фильтр параметр 'Освобождаются с'
     *      ~ Дата вида 2022-08-19 или пусто
     *
     * @var string
     */
    public string $filter_free_date_from = '';

    /**
     * Фильтр параметр 'Освобождаются по'
     *      ~ Дата вида 2022-08-19 или пусто
     *
     * @var string
     */
    public string $filter_free_date_to = '';

    /**
     * Фильтр параметр 'Регистратор'
     *      ~ Список регистраторов (массив)
     *      ~ См константы вида RegRuFilter::FILTER_REGISTRAR_..
     *
     * @var array
     */
    public array $filter_registrar = [];

    /**
     * Фильтр параметр 'Page' (pg=)
     *      ~ Постраничная навигация
     *      ~ Значниея 1 - 999999
     *
     * @var int
     */
    protected int $pg = 1;

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * Constructor
     * @param array $parameters
     * @param int $page
     */
    public function __construct(array $parameters = [], $page = 1)
    {
        // set page
        $this->pg = $page;

        // set parameters
        $this->setParameters($parameters);
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
     * Set filter parameters
     * @param array $args
     * @return void
     */
    public function setParameters(array $args = [])
    {
        $this->parameters = get_object_vars($this);
        foreach($args as $key => $value) {
            if (isset($this->parameters[$key])) {
                $this->{$key} = $value;
            }
        }

        $this->set_filter_args($args, 'filter_expiring');
        $this->set_filter_args($args, 'filter_dnames');
        $this->set_filter_args($args, 'filter_symbols_from');
        $this->set_filter_args($args, 'filter_symbols_to');
        $this->set_filter_args($args, 'filter_withoutdash');
        $this->set_filter_args($args, 'filter_withoutnumber');
        $this->set_filter_args($args, 'filter_withnumber');
        $this->set_filter_args($args, 'filter_zone_ru');
        $this->set_filter_args($args, 'filter_zone_rf');
        $this->set_filter_args($args, 'filter_zone_su');
        $this->set_filter_args($args, 'filter_tic_from');
        $this->set_filter_args($args, 'filter_tic_to');
        $this->set_filter_args($args, 'filter_pr_from');
        $this->set_filter_args($args, 'filter_pr_to');
        $this->set_filter_args($args, 'filter_current_bid_from');
        $this->set_filter_args($args, 'filter_current_bid_to');
        $this->set_filter_args($args, 'filter_limit_bid_from');
        $this->set_filter_args($args, 'filter_limit_bid_to');
        $this->set_filter_args($args, 'filter_bid_type');
        $this->set_filter_args($args, 'filter_creation_date_from');
        $this->set_filter_args($args, 'filter_creation_date_to');
        $this->set_filter_args($args, 'filter_free_date_from');
        $this->set_filter_args($args, 'filter_free_date_to');
        $this->set_filter_args($args, 'filter_registrar');
    }

    /**
     * Get filter parameters
     * @return array
     */
    public function getParameters() : array
    {
        return get_object_vars($this);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->{$key};
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isset(string $key)
    {
        return isset($this->getParameters()[$key]);
    }

    /**
     * Set filter arguments to this parameter values
     * @param array $args
     * @param string $key
     * @return void
     */
    protected function set_filter_args(array $args, string $key)
    {
        // set anonymous filter
        /**
         * Prepare array filter parameter
         */
        if (isset($args[ $key ]) && is_array($args[ $key ])) {
            $this->parameters[ $key ] = implode(',', array_map(function($value) use($key) {
                return $key . '=' . $value;
            }));
        }
        /**
         * Prepare string or int filter parameter
         */
        elseif (isset($args[ $key ]) && (is_numeric($args[ $key ]) || is_string($args[ $key ]))) {
            $this->parameters[ $key ] = $key . '=' . $args[ $key ];
        }
        /**
         * Unset filter parameter
         */
        else {
            unset($this->parameters[ $key ]);
        }
    }

    /**
     * Set page paginate
     * @param int $page
     * @return void
     */
    public function setPage(int $page = 1)
    {
        if ($page == 1) {
            unset($this->parameters['pg']);
        } else {
            $pg = ($page - 1);

            $this->parameters['pg'] = 'pg=' . $pg;
        }

        $this->pg = $page;
    }

    /**
     * Get page paginate
     * @return int
     */
    public function getPage() : int
    {
        return $this->pg;
    }

    /**
     * Check Csv filter [ manual filter ]
     * @param array $values
     * @return bool
     */
    public function check_filter(array $values = [])
    {
        $checker = true;

        if (isset($values['domain'])) {
            $values['domain'] = strtolower($values['domain']);
        }

        /**
         * dnames
         */
        if ($this->isset('filter_dnames') && isset($values['domain']) && !empty($values['domain'])) {
            $checker = $checker && preg_split('/(\n\r\s)/', $values['domain']);
        }

        /**
         * price
         */
        if ($this->isset('filter_pr_from') && isset($values['price']) && !empty($values['price'])) {
            $checker = $checker && ($values['price'] > $this->get('filter_pr_from'));
        }
        if ($this->isset('filter_pr_to') && $this->get('filter_pr_to') != 0 && isset($values['price']) && !empty($values['price'])) {
            $checker = $checker && ($values['price'] < $this->get('filter_pr_to'));
        }

        /**
         * free
         */
        if ($this->isset('filter_free_date_from') && isset($values['free']) && !empty($values['free'])) {
            $checker = $checker && ($values['free'] > $this->get('filter_free_date_from'));
        }
        if ($this->isset('filter_free_date_to') && !empty($this->get('filter_free_date_to')) && isset($values['free']) && !empty($values['free'])) {
            $checker = $checker && ($values['free'] < $this->get('filter_free_date_to'));
        }

        /**
         * registrar
         */
        if ($this->isset('filter_registrar') && !empty($this->get('filter_registrar')) && isset($values['registrar']) && !empty($values['registrar'])) {
            $filter_registrar = [];
            foreach ($this->get('filter_registrar') as $f_reg) {
                $filter_registrar[] = strtoupper($f_reg);
            }
            $checker = $checker && in_array($values['registrar'], $filter_registrar);
        }

        /**
         * without
         */
        if ($this->isset('filter_withoutdash') && $this->get('filter_withoutdash') !== 0 && isset($values['domain']) && !empty($values['domain'])) {
            $checker = $checker && (strpos('-', $values['domain']) !== false);
        }
        if ($this->isset('filter_withoutnumber') && $this->get('filter_withoutnumber') !== 0 && isset($values['domain']) && !empty($values['domain'])) {
            $checker = $checker && preg_match('/[A-Za-z\-\.]/', $values['domain']);
        }
        if ($this->isset('filter_withnumber') && $this->get('filter_withnumber') != 0 && isset($values['domain']) && !empty($values['domain'])) {
            $checker = $checker && preg_match('/[A-Za-z\-\.]/', $values['domain']) && preg_match('/[0-9]/', $values['domain']);
        }

        /**
         * zone
         */
        if ((
                ( $this->isset('filter_zone_ru') && !empty($this->get('filter_zone_ru')) ) ||
                ( $this->isset('filter_zone_rf') && !empty($this->get('filter_zone_rf')) ) ||
                ( $this->isset('filter_zone_su') && !empty($this->get('filter_zone_su')) )
            ) && isset($values['domain']) && !empty($values['domain'])
        ) {
            $parts = explode('.', parse_url($values['domain'], PHP_URL_PATH));
            $zone = end($parts);
        }
        if ($this->isset('filter_zone_ru') && !empty($this->get('filter_zone_ru'))) {
            $checker = $checker && ('ru' == $zone);
        }
        if ($this->isset('filter_zone_rf') && !empty($this->get('filter_zone_rf'))) {
            $checker = $checker && ('рф' == $zone);
        }
        if ($this->isset('filter_zone_su') && !empty($this->get('filter_zone_su'))) {
            $checker = $checker && ('su' == $zone);
        }

        return $checker;
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
