<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class MixerBrSpecialContentFilter
 */
class MixerBrSpecialContentFilter extends  AbstractContentFilter implements IYamlConfigFilter
{
    /**
     * @var string
     */
    protected $name = 'mixer_br_special';

    /**
     * @return void
     */
    public function configure() : void
    {
        $this->setOption('numbers', [], 'is_array|is_null|empty');
        $this->setOption('random_others', false, 'is_int|is_bool|is_null|empty');
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function doFilter(string $text, array $options = []) : string
    {
        $outer = preg_split('/<br[^>]*><br[^>]*>/i', $text);
        if (is_string($outer) || (is_array($outer) && count($outer) == 1)) {
            return $text;
        }

//        $mix = $this->mix(array_keys($options['numbers']), $outer, $options);
        $mix = $outer;
        $res = [];
        for ($n = 0; $n < count($mix); $n++) {
            $inner = preg_split('/<br[^>]*>/i', $mix[$n]);
            if (array_key_exists($n, $options['numbers'])) {
               $e = $this->mix($options['numbers'][$n], $inner, $options);
                $res[$n] = implode('<br>', $e);
                dump($e);
            } else {
                $res[$n] = implode('<br>', $inner);
            }
        }

        return implode('<br><br>', $res);
    }

    /**
     * @param array $numbers
     * @param array $array
     * @param array $options
     * @return array
     */
    protected function mix(array $numbers, array &$array, array $options = [])
    {
        $mix = [];
        foreach ($numbers as $k => $val) {
            if (array_key_exists($k, $array)) {
                $mix[] = trim($array[$k]);
                unset($array[$k]);
            }
        }

        if (isset($options['random_others']) && ($options['random_others'] == 'true' || $options['random_others'] == '1')) {
            if (count($array) == 1) {
                $mix[] = trim(end($array));
                unset($array[key($array)]);
            } elseif (count($array) > 1) {
                $rand = array_rand($array, count($array));
                foreach ($rand as $rn) {
                    $mix[] = trim($array[$rn]);
                }
            }
        }
        dump(['mix' => $mix]);
        return $mix;
    }

}
