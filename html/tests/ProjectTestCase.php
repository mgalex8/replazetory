<?php
namespace Tests;

use App\Bundle\TemplateThemes\AnyParameterTemplateTheme;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;

class ProjectTestCase extends TestCase implements SelfDescribing, Test
{

    /**
     * @param string $path
     * @return string
     */
    protected function abs_path(string $path = '' )
    {
        if (! defined('ABS_PATH')) define('ABS_PATH', __DIR__.'/..');
        return ABS_PATH . ltrim($path, \DIRECTORY_SEPARATOR);
    }

    /**
     * @param array $array
     * @param $value
     * @return void
     */
    protected function assertArrayEqualsStringValues(array $array, $value, int $max_iterator = 0)
    {
        foreach ($array as $arr) {
            $this->assertEquals($arr, $value);
            if ($max_iterator > 0 && $index++ >= $max_iterator) break;
        }
    }

    /**
     * @param array $array
     * @param $value
     * @param int $max
     * @return void
     */
    protected function assertArrayValuesEqualsType(array $array, $value, int $max_iterator = 0)
    {
        $valueType = is_object($value) ? get_class($value) : gettype($value);
        foreach ($array as $arr) {
            if (is_object($arr)) {
                $this->assertEquals(get_class($arr), $valueType);
            } else {
                $this->assertEquals(gettype($arr), $valueType);
            }
            if ($max_iterator > 0 && $index++ >= $max_iterator) break;
        }
    }

    /**
     * @param array $array
     * @param $value
     * @param int $max
     * @return void
     */
    protected function assertArrayContainsString(array $array, $value, int $max_iterator = 0)
    {
        $index = 0;
        foreach ($array as $arr) {
            $this->assertEquals($arr, $value);
            if ($max_iterator > 0 && $index++ >= $max_iterator) break;
        }
    }


}