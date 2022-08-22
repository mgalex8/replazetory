<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class MixerBrContentFilter
 */
class MixerBrContentFilter extends AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'mixer_br';

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        $exploded = explode('<br>', $text);
        if (! is_array($exploded)) {
            return $exploded;
        }
        $rand = null;
        if (is_array($exploded) && count($exploded) > 1) {
            shuffle($exploded);
        }
        if ($this->optionEqual($options, 'trim', 1)) {
            $exploded = $this->processArrayValuesWithTrim($exploded, [], $options);
        }
        return implode('<br>', $exploded );
    }

    /**
     * @param array $array
     * @param array $options
     * @return array
     */
    protected function processRandArrayValuesWithTrim(array $array, array $rand, array $options)
    {
        $res = [];
        foreach ($rand as $rn) {
            $res[] = preg_replace(['/\n/', '/\t/', '/\r/'], ['','',''], trim($array[$rn]));
        }
        return $res;
    }

    /**
     * @param array $array
     * @param array $options
     * @return array
     */
    protected function processArrayValuesWithTrim(array $array, array $rand, array $options)
    {
        $res = [];
        foreach ($array as $val) {
            $res[] = preg_replace(['/\n/', '/\t/', '/\r/'], ['','', ''], trim($val));
        }
        return $res;
    }

    /**
     * @param array $array
     * @param array $options
     * @return array
     */
    protected function processRandArrayValues(array $array, array $rand, array $options)
    {
        $res = [];
        foreach ($rand as $rn) {
            $res[] = $array[$rn];
        }
        return $res;
    }

}
