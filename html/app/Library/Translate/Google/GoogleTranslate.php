<?php
namespace App\Library\Translate\Google;

use App\Library\Translate\Google\Result\GoogleTranslateResultAbstract;
use App\Library\Translate\Google\Result\GoogleTranslateResultSuccess;
use App\Library\Translate\Google\Result\GoogleTranslateResultError;

/**
 * GoogleTranslate class
 */
class GoogleTranslate
{

    /**
     * @const string
     */
    public const GOOGLE_TRANSLATE_LANGUAGE_AUTO_DETECT          = 'auto';
    public const GOOGLE_TRANSLATE_LANGUAGE_Afrikaans            = 'af';
    public const GOOGLE_TRANSLATE_LANGUAGE_Albanian             = 'sq';
    public const GOOGLE_TRANSLATE_LANGUAGE_Amharic              = 'am';
    public const GOOGLE_TRANSLATE_LANGUAGE_Arabic               = 'ar';
    public const GOOGLE_TRANSLATE_LANGUAGE_Armenian             = 'hy';
    public const GOOGLE_TRANSLATE_LANGUAGE_Azerbaijani          = 'az';
    public const GOOGLE_TRANSLATE_LANGUAGE_Basque               = 'eu';
    public const GOOGLE_TRANSLATE_LANGUAGE_Belarusian           = 'be';
    public const GOOGLE_TRANSLATE_LANGUAGE_Bengali              = 'bn';
    public const GOOGLE_TRANSLATE_LANGUAGE_Bosnian              = 'bs';
    public const GOOGLE_TRANSLATE_LANGUAGE_Bulgarian            = 'bg';
    public const GOOGLE_TRANSLATE_LANGUAGE_Catalan              = 'ca';
    public const GOOGLE_TRANSLATE_LANGUAGE_Cebuano              = 'ceb';
    public const GOOGLE_TRANSLATE_LANGUAGE_Chinese_Simplified   = 'zh-CN';
    public const GOOGLE_TRANSLATE_LANGUAGE_Chinese_Traditional  = 'zh-TW';
    public const GOOGLE_TRANSLATE_LANGUAGE_Corsican             = 'co';
    public const GOOGLE_TRANSLATE_LANGUAGE_Croatian             = 'hr';
    public const GOOGLE_TRANSLATE_LANGUAGE_Czech                = 'cs';
    public const GOOGLE_TRANSLATE_LANGUAGE_Danish               = 'da';
    public const GOOGLE_TRANSLATE_LANGUAGE_Dutch                = 'nl';
    public const GOOGLE_TRANSLATE_LANGUAGE_English              = 'en';
    public const GOOGLE_TRANSLATE_LANGUAGE_Esperanto            = 'eo';
    public const GOOGLE_TRANSLATE_LANGUAGE_Estonian             = 'et';
    public const GOOGLE_TRANSLATE_LANGUAGE_Finnish              = 'fi';
    public const GOOGLE_TRANSLATE_LANGUAGE_French               = 'fr';
    public const GOOGLE_TRANSLATE_LANGUAGE_Frisian              = 'fy';
    public const GOOGLE_TRANSLATE_LANGUAGE_Galician             = 'gl';
    public const GOOGLE_TRANSLATE_LANGUAGE_Georgian             = 'ka';
    public const GOOGLE_TRANSLATE_LANGUAGE_German               = 'de';
    public const GOOGLE_TRANSLATE_LANGUAGE_Greek                = 'el';
    public const GOOGLE_TRANSLATE_LANGUAGE_Gujarati             = 'gu';
    public const GOOGLE_TRANSLATE_LANGUAGE_Haitian              = 'ht';
    public const GOOGLE_TRANSLATE_LANGUAGE_Hausa                = 'ha';
    public const GOOGLE_TRANSLATE_LANGUAGE_Hawaiian             = 'haw';
    public const GOOGLE_TRANSLATE_LANGUAGE_Hebrew               = 'he**'; // iv
    public const GOOGLE_TRANSLATE_LANGUAGE_Hindi                = 'hi';
    public const GOOGLE_TRANSLATE_LANGUAGE_Hmong                = 'hmn';
    public const GOOGLE_TRANSLATE_LANGUAGE_Hungarian            = 'hu';
    public const GOOGLE_TRANSLATE_LANGUAGE_Icelandic            = 'is';
    public const GOOGLE_TRANSLATE_LANGUAGE_Igbo                 = 'ig';
    public const GOOGLE_TRANSLATE_LANGUAGE_Indonesian           = 'id';
    public const GOOGLE_TRANSLATE_LANGUAGE_Irish                = 'ga';
    public const GOOGLE_TRANSLATE_LANGUAGE_Italian              = 'it';
    public const GOOGLE_TRANSLATE_LANGUAGE_Japanese             = 'ja';
    public const GOOGLE_TRANSLATE_LANGUAGE_Javanese             = 'jw';
    public const GOOGLE_TRANSLATE_LANGUAGE_Kannada              = 'kn';
    public const GOOGLE_TRANSLATE_LANGUAGE_Kazakh               = 'kk';
    public const GOOGLE_TRANSLATE_LANGUAGE_Khmer                = 'km';
    public const GOOGLE_TRANSLATE_LANGUAGE_Korean               = 'ko';
    public const GOOGLE_TRANSLATE_LANGUAGE_Kurdish              = 'ku';
    public const GOOGLE_TRANSLATE_LANGUAGE_Kyrgyz               = 'ky';
    public const GOOGLE_TRANSLATE_LANGUAGE_Lao                  = 'lo';
    public const GOOGLE_TRANSLATE_LANGUAGE_Latin                = 'la';
    public const GOOGLE_TRANSLATE_LANGUAGE_Latvian              = 'lv';
    public const GOOGLE_TRANSLATE_LANGUAGE_Lithuanian           = 'lt';
    public const GOOGLE_TRANSLATE_LANGUAGE_Luxembourgish        = 'lb';
    public const GOOGLE_TRANSLATE_LANGUAGE_Macedonian           = 'mk';
    public const GOOGLE_TRANSLATE_LANGUAGE_Malagasy             = 'mg';
    public const GOOGLE_TRANSLATE_LANGUAGE_Malay                = 'ms';
    public const GOOGLE_TRANSLATE_LANGUAGE_Malayalam            = 'ml';
    public const GOOGLE_TRANSLATE_LANGUAGE_Maltese              = 'mt';
    public const GOOGLE_TRANSLATE_LANGUAGE_Maori                = 'mi';
    public const GOOGLE_TRANSLATE_LANGUAGE_Marathi              = 'mr';
    public const GOOGLE_TRANSLATE_LANGUAGE_Mongolian            = 'mn';
    public const GOOGLE_TRANSLATE_LANGUAGE_Myanmar              = 'my';
    public const GOOGLE_TRANSLATE_LANGUAGE_Nepali               = 'ne';
    public const GOOGLE_TRANSLATE_LANGUAGE_Norwegian            = 'no';
    public const GOOGLE_TRANSLATE_LANGUAGE_Nyanja               = 'ny';
    public const GOOGLE_TRANSLATE_LANGUAGE_Pashto               = 'ps';
    public const GOOGLE_TRANSLATE_LANGUAGE_Persian              = 'fa';
    public const GOOGLE_TRANSLATE_LANGUAGE_Polish               = 'pl';
    public const GOOGLE_TRANSLATE_LANGUAGE_Portuguese           = 'pt';
    public const GOOGLE_TRANSLATE_LANGUAGE_Punjabi              = 'pa';
    public const GOOGLE_TRANSLATE_LANGUAGE_Romanian             = 'ro';
    public const GOOGLE_TRANSLATE_LANGUAGE_Russian              = 'ru';
    public const GOOGLE_TRANSLATE_LANGUAGE_Samoan               = 'sm';
    public const GOOGLE_TRANSLATE_LANGUAGE_Scots                = 'gd';
    public const GOOGLE_TRANSLATE_LANGUAGE_Serbian              = 'sr';
    public const GOOGLE_TRANSLATE_LANGUAGE_Sesotho              = 'st';
    public const GOOGLE_TRANSLATE_LANGUAGE_Shona                = 'sn';
    public const GOOGLE_TRANSLATE_LANGUAGE_Sindhi               = 'sd';
    public const GOOGLE_TRANSLATE_LANGUAGE_Sinhala              = 'si';
    public const GOOGLE_TRANSLATE_LANGUAGE_Slovak               = 'sk';
    public const GOOGLE_TRANSLATE_LANGUAGE_Slovenian            = 'sl';
    public const GOOGLE_TRANSLATE_LANGUAGE_Somali               = 'so';
    public const GOOGLE_TRANSLATE_LANGUAGE_Spanish              = 'es';
    public const GOOGLE_TRANSLATE_LANGUAGE_Sundanese            = 'su';
    public const GOOGLE_TRANSLATE_LANGUAGE_Swahili              = 'sw';
    public const GOOGLE_TRANSLATE_LANGUAGE_Swedish              = 'sv';
    public const GOOGLE_TRANSLATE_LANGUAGE_Tagalog              = 'tl';
    public const GOOGLE_TRANSLATE_LANGUAGE_Tajik                = 'tg';
    public const GOOGLE_TRANSLATE_LANGUAGE_Tamil                = 'ta';
    public const GOOGLE_TRANSLATE_LANGUAGE_Telugu               = 'te';
    public const GOOGLE_TRANSLATE_LANGUAGE_Thai                 = 'th';
    public const GOOGLE_TRANSLATE_LANGUAGE_Turkish              = 'tr';
    public const GOOGLE_TRANSLATE_LANGUAGE_Ukrainian            = 'uk';
    public const GOOGLE_TRANSLATE_LANGUAGE_Urdu                 = 'ur';
    public const GOOGLE_TRANSLATE_LANGUAGE_Uzbek                = 'uz';
    public const GOOGLE_TRANSLATE_LANGUAGE_Vietnamese           = 'vi';
    public const GOOGLE_TRANSLATE_LANGUAGE_Welsh                = 'cy';
    public const GOOGLE_TRANSLATE_LANGUAGE_Xhosa                = 'xh';
    public const GOOGLE_TRANSLATE_LANGUAGE_Yiddish              = 'yi';
    public const GOOGLE_TRANSLATE_LANGUAGE_Yoruba               = 'yo';
    public const GOOGLE_TRANSLATE_LANGUAGE_Zulu                 = 'zu';

    /**
     * @var string[]
     */
    protected $script_urls = [
        'https://script.google.com/macros/s/AKfycbxqo9aGmHWwrmZpKWOevzhzTTtuPA3-bkXNNKjNjohyf9g6R_u1Fs5TGpbCqHCswAR1/exec'
    ];

    /**
     * @var null|GoogleTranslateResultSuccess|GoogleTranslateResultError
     */
    protected $data;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array|null
     */
    protected $curl_info;

    /**
     * Class GoogleTranslate Constructor.
     */
    public function __construct()
    {
        $this->data = null;
        $this->errors = [];
        $this->curl_info = null;
    }

    /**
     * Translate `text` from `source_lang` language to `target_lang`
     * with Google Translate Scripts
     *
     * @return void
     */
    public function translate(string $text, string $source_lang, string $target_lang)
    {
        $data = null;
        $this->data = null;
        $this->errors = null;

        $request_url = $this->getScriptUrl();
        $parameters = [
            'source_lang' => $source_lang,
            'target_lang' => $target_lang,
            'text' => $text,
        ];

        /**
         * Check input parameters
         */
        $this->checkParametersAndThrowException($parameters);

        /**
         * send request
         */
        $response = $this->curl_request($request_url, $parameters);
        $response_code = $this->getLastResponseCurlInfo('response_code');
        $success_response_code = $this->successStatusCode($response_code);

        /**
         * return result
         */
        if ($success_response_code) {
            $data = json_decode($response);
        }

        /**
         * Make output result
         */
        if (! $success_response_code) {
            $data = (object) array_merge($parameters, ['status' => 'error', 'errors' => [sprintf('Bad request: return status code %s', $response_code)]]);
            dump($data);
            $this->data = new GoogleTranslateResultError($data);
            $this->errors = $data->errors;
        } elseif(empty($data)) {
            $data = (object) array_merge($parameters, ['status' => 'error', 'errors' => ['JSON decode function return empty value']]);
            $this->data = new GoogleTranslateResultError($data);
            $this->errors = $data->errors;
        } elseif ($data->status == 'error') {
            $this->data = new GoogleTranslateResultError($data);
            $this->errors = $data->errors;
        } elseif ($data->status == 'success') {
            $this->data = new GoogleTranslateResultSuccess($data);
        } else {
            $data = (object) array_merge($parameters, ['status' => 'error', 'errors' => ['Unknown error']]);
            $this->data = new GoogleTranslateResultError($data);
            $this->errors = $data->errors;
        }

        // set request code
        $this->data->setResponseCode($response_code);

        return $this->data;
    }

    /**
     * Get Last response result
     * @return object|null
     */
    public function getLastResponseResult()
    {
        return $this->data;
    }

    /**
     * Get Last response result
     * @return object|null
     */
    public function getLastResponseErrors()
    {
        return $this->errors;
    }

    /**
     * Get Last response curl info
     * @param string|null $key
     * @return array|int|float|string|null|mixed
     * @throws \Exception
     */
    public function getLastResponseCurlInfo(string $key = null)
    {
        if (null !== $key && is_array($this->curl_info)) {
            if (! in_array($key, $this->curl_info)) {
                throw new \Exception(sprintf('Unknown parameter `%s` in curl response parameter array, see argument 0 for method %s::%s', $key, __CLASS__, __METHOD__));
            }
            return $this->curl_info[ $key ];
        }
        return $this->curl_info;
    }

    /**
     * @param int|null $index
     * @param bool $random
     * @return string
     */
    public function getScriptUrl(?int $index = null, bool $random = true)
    {
        $id = $index == null ? (count($this->script_urls) > 1 ? rand(0, count($this->script_urls) - 1) : 0) : $index;

        return $this->script_urls[ $id ];
    }

    /**
     * @param string $url
     * @param array $parameters
     * @return bool|string
     */
    protected function curl_request(string $url, array $parameters)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $parameters,
        ]);
        $result = curl_exec($ch);
        $this->curl_info = curl_getinfo($ch);

        if (isset($this->curl_info['http_code']) && ! isset($this->curl_info['response_code'])) {
            $this->curl_info['response_code'] = $this->curl_info['http_code'];
        }
        if (is_string($this->curl_info['response_code'])) {
            $this->curl_info['response_code'] = (int) $this->curl_info['response_code'];
        }

        return $result;
    }

    /**
     * @param int $response_code
     * @return bool
     */
    protected function successStatusCode(int $response_code)
    {
        $match = (int) floor($response_code / 100) * 100;
        if ($match == 200 || $match == 300) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $parameters
     * @return void
     */
    protected function checkParametersAndThrowException(array $parameters)
    {
        $language_codes = $this->getLanguageCodes();
        if (! in_array($parameters['source_lang'], $language_codes)) {
            throw new \Exception(sprintf('Not Supported Language Code` %s` for argument %s name `%s`', $parameters['source_lang'], 1, 'source_lang'));
        }
        elseif (! in_array($parameters['target_lang'], $language_codes)) {
            throw new \Exception(sprintf('Not Supported Language Code` %s` for argument %s name `%s`', $parameters['target_lang'], 1, 'target_lang'));
        }

    }

    /**
     * Get supported language codes
     * @return int[]|string[]
     */
    public function getLanguageCodes()
    {
        return array_keys($this->getLanguageNames());
    }

    /**
     * Get supported language names
     * @return string[]
     */
    public function getLanguageNames()
    {
        return [
            self::GOOGLE_TRANSLATE_LANGUAGE_AUTO_DETECT          => 'Auto-detect',
            self::GOOGLE_TRANSLATE_LANGUAGE_Afrikaans            => 'Afrikaans',
            self::GOOGLE_TRANSLATE_LANGUAGE_Albanian             => 'Albanian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Amharic              => 'Amharic',
            self::GOOGLE_TRANSLATE_LANGUAGE_Arabic               => 'Arabic',
            self::GOOGLE_TRANSLATE_LANGUAGE_Armenian             => 'Armenian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Azerbaijani          => 'Azerbaijani',
            self::GOOGLE_TRANSLATE_LANGUAGE_Basque               => 'Basque',
            self::GOOGLE_TRANSLATE_LANGUAGE_Belarusian           => 'Belarusian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Bengali              => 'Bengali',
            self::GOOGLE_TRANSLATE_LANGUAGE_Bosnian              => 'Bosnian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Bulgarian            => 'Bulgarian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Catalan              => 'Catalan',
            self::GOOGLE_TRANSLATE_LANGUAGE_Cebuano              => 'Cebuano',
            self::GOOGLE_TRANSLATE_LANGUAGE_Chinese_Simplified   => 'Chinese (Simplified)',
            self::GOOGLE_TRANSLATE_LANGUAGE_Chinese_Traditional  => 'Chinese (Traditional)',
            self::GOOGLE_TRANSLATE_LANGUAGE_Corsican             => 'Corsican',
            self::GOOGLE_TRANSLATE_LANGUAGE_Croatian             => 'Croatian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Czech                => 'Czech',
            self::GOOGLE_TRANSLATE_LANGUAGE_Danish               => 'Danish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Dutch                => 'Dutch',
            self::GOOGLE_TRANSLATE_LANGUAGE_English              => 'English',
            self::GOOGLE_TRANSLATE_LANGUAGE_Esperanto            => 'Esperanto',
            self::GOOGLE_TRANSLATE_LANGUAGE_Estonian             => 'Estonian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Finnish              => 'Finnish',
            self::GOOGLE_TRANSLATE_LANGUAGE_French               => 'French',
            self::GOOGLE_TRANSLATE_LANGUAGE_Frisian              => 'Frisian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Galician             => 'Galician',
            self::GOOGLE_TRANSLATE_LANGUAGE_Georgian             => 'Georgian',
            self::GOOGLE_TRANSLATE_LANGUAGE_German               => 'German',
            self::GOOGLE_TRANSLATE_LANGUAGE_Greek                => 'Greek',
            self::GOOGLE_TRANSLATE_LANGUAGE_Gujarati             => 'Gujarati',
            self::GOOGLE_TRANSLATE_LANGUAGE_Haitian              => 'Haitian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Hausa                => 'Hausa',
            self::GOOGLE_TRANSLATE_LANGUAGE_Hawaiian             => 'Hawaiian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Hebrew               => 'Hebrew',
            self::GOOGLE_TRANSLATE_LANGUAGE_Hindi                => 'Hindi',
            self::GOOGLE_TRANSLATE_LANGUAGE_Hmong                => 'Hmong',
            self::GOOGLE_TRANSLATE_LANGUAGE_Hungarian            => 'Hungarian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Icelandic            => 'Icelandic',
            self::GOOGLE_TRANSLATE_LANGUAGE_Igbo                 => 'Igbo',
            self::GOOGLE_TRANSLATE_LANGUAGE_Indonesian           => 'Indonesian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Irish                => 'Irish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Italian              => 'Italian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Japanese             => 'Japanese',
            self::GOOGLE_TRANSLATE_LANGUAGE_Javanese             => 'Javanese',
            self::GOOGLE_TRANSLATE_LANGUAGE_Kannada              => 'Kannada',
            self::GOOGLE_TRANSLATE_LANGUAGE_Kazakh               => 'Kazakh',
            self::GOOGLE_TRANSLATE_LANGUAGE_Khmer                => 'Khmer',
            self::GOOGLE_TRANSLATE_LANGUAGE_Korean               => 'Korean',
            self::GOOGLE_TRANSLATE_LANGUAGE_Kurdish              => 'Kurdish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Kyrgyz               => 'Kyrgyz',
            self::GOOGLE_TRANSLATE_LANGUAGE_Lao                  => 'Lao',
            self::GOOGLE_TRANSLATE_LANGUAGE_Latin                => 'Latin',
            self::GOOGLE_TRANSLATE_LANGUAGE_Latvian              => 'Latvian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Lithuanian           => 'Lithuanian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Luxembourgish        => 'Luxembourgish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Macedonian           => 'Macedonian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Malagasy             => 'Malagasy',
            self::GOOGLE_TRANSLATE_LANGUAGE_Malay                => 'Malay',
            self::GOOGLE_TRANSLATE_LANGUAGE_Malayalam            => 'Malayalam',
            self::GOOGLE_TRANSLATE_LANGUAGE_Maltese              => 'Maltese',
            self::GOOGLE_TRANSLATE_LANGUAGE_Maori                => 'Maori',
            self::GOOGLE_TRANSLATE_LANGUAGE_Marathi              => 'Marathi',
            self::GOOGLE_TRANSLATE_LANGUAGE_Mongolian            => 'Mongolian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Myanmar              => 'Myanmar',
            self::GOOGLE_TRANSLATE_LANGUAGE_Nepali               => 'Nepali',
            self::GOOGLE_TRANSLATE_LANGUAGE_Norwegian            => 'Norwegian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Nyanja               => 'Nyanja',
            self::GOOGLE_TRANSLATE_LANGUAGE_Pashto               => 'Pashto',
            self::GOOGLE_TRANSLATE_LANGUAGE_Persian              => 'Persian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Polish               => 'Polish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Portuguese           => 'Portuguese',
            self::GOOGLE_TRANSLATE_LANGUAGE_Punjabi              => 'Punjabi',
            self::GOOGLE_TRANSLATE_LANGUAGE_Romanian             => 'Romanian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Russian              => 'Russian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Samoan               => 'Samoan',
            self::GOOGLE_TRANSLATE_LANGUAGE_Scots                => 'Scots',
            self::GOOGLE_TRANSLATE_LANGUAGE_Serbian              => 'Serbian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Sesotho              => 'Sesotho',
            self::GOOGLE_TRANSLATE_LANGUAGE_Shona                => 'Shona',
            self::GOOGLE_TRANSLATE_LANGUAGE_Sindhi               => 'Sindhi',
            self::GOOGLE_TRANSLATE_LANGUAGE_Sinhala              => 'Sinhala',
            self::GOOGLE_TRANSLATE_LANGUAGE_Slovak               => 'Slovak',
            self::GOOGLE_TRANSLATE_LANGUAGE_Slovenian            => 'Slovenian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Somali               => 'Somali',
            self::GOOGLE_TRANSLATE_LANGUAGE_Spanish              => 'Spanish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Sundanese            => 'Sundanese',
            self::GOOGLE_TRANSLATE_LANGUAGE_Swahili              => 'Swahili',
            self::GOOGLE_TRANSLATE_LANGUAGE_Swedish              => 'Swedish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Tagalog              => 'Tagalog',
            self::GOOGLE_TRANSLATE_LANGUAGE_Tajik                => 'Tajik',
            self::GOOGLE_TRANSLATE_LANGUAGE_Tamil                => 'Tamil',
            self::GOOGLE_TRANSLATE_LANGUAGE_Telugu               => 'Telugu',
            self::GOOGLE_TRANSLATE_LANGUAGE_Thai                 => 'Thai',
            self::GOOGLE_TRANSLATE_LANGUAGE_Turkish              => 'Turkish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Ukrainian            => 'Ukrainian',
            self::GOOGLE_TRANSLATE_LANGUAGE_Urdu                 => 'Urdu',
            self::GOOGLE_TRANSLATE_LANGUAGE_Uzbek                => 'Uzbek',
            self::GOOGLE_TRANSLATE_LANGUAGE_Vietnamese           => 'Vietnamese',
            self::GOOGLE_TRANSLATE_LANGUAGE_Welsh                => 'Welsh',
            self::GOOGLE_TRANSLATE_LANGUAGE_Xhosa                => 'Xhosa',
            self::GOOGLE_TRANSLATE_LANGUAGE_Yiddish              => 'Yiddish',
            self::GOOGLE_TRANSLATE_LANGUAGE_Yoruba               => 'Yoruba',
            self::GOOGLE_TRANSLATE_LANGUAGE_Zulu                 => 'Zulu',
        ];
    }



}
