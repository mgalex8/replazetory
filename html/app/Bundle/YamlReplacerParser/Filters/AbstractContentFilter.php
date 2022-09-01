<?php
namespace App\Bundle\YamlReplacerParser\Filters;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class AbstractContentFilter
 */
abstract class AbstractContentFilter implements IYamlConfigFilter
{

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return void
     */
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Set validators for option
     * @param string $name
     * @param string|int|bool|array|null $value
     * @param string|array|null $validator_functions
     * @return void
     */
    protected function setOptionValidator(string $name, $value = null, $validator_functions = null) : void
    {
        if ($validator_functions) {
            $this->options[$name] = [
                'value' => $value,
                'validators' => $this->__explode_validators($name, $validator_functions),
            ];
        } elseif ($this->hasOption($this->options, $name)) {
            $this->options[$name]['value'] = $value;
        } else {
            $this->options[$name] = [
                'value' => $value,
                'validators' => [],
            ];
        }
    }

    /**
     * Duplicate function setOptionValidator with other name setOption
     * @param string $name
     * @param string|int|bool|array|null $value
     * @param array|string|null $validator_functions
     * @return void
     */
    public function setOption(string $name, $value = null, $validator_functions = null) : void
    {
        $this->setOptionValidator($name, $value, $validator_functions);
    }

    /**
     * Set many options all
     * @param array $options
     * @param array $validator_functions_array
     * @return void
     */
    public function setOptionsAll(array $options = [], array $validator_functions_array = array()) : void
    {
        $index = 0;
        foreach ($options as $name => $value) {
            if ($this->hasOption($this->options, $name)) {
                if ($validator_functions_array && $this->validateOption($name, $value, $validator_functions_array[$index])) {
                    $this->setOption($name, $value, $validator_functions_array[$index]);
                } elseif (! $validator_functions_array && $this->validateOption($name, $value, $this->options[$name]['validators'])) {
                    $this->setOption($name, $value, $this->options[$name]['validators']);
                }
            }
            $index++;
        }
    }

    /**
     * Validate option once
     * @param $name
     * @param $value
     * @param $validator_functions
     * @return bool
     */
    public function validateOption($name, $value = null, $validator_functions = null) : bool
    {
        $valid = true;

        /**
         * Validation all functions
         */
        if ($validator_functions !== null) {
            $validator_functions = $this->__explode_validators($name, $validator_functions);
        } elseif (isset($this->options[$name])) {
            $validator_functions = $this->options[$name]['validators'];
        } else {
            $validator_functions = [];
        }

        /**
         * Check values
         */
        foreach ($validator_functions as $validator) {
            if (! function_exists($validator)) {
                throw new \BadFunctionCallException('Call unknown function ' . $validator);
            }
            $valid = $valid && $validator($value);
        }

        /**
         * Return bool value
         */
        return $valid;
    }

    /**
     * Explode validators as array
     * @param string $name
     * @param string|array|null $validator_functions
     * @return array
     */
    protected function __explode_validators(string $name, $validator_functions = null) : array
    {
        if (is_string($validator_functions)) {
            $validator_functions = explode('|', $validator_functions);
        } elseif (is_array($validator_functions)) {
            $validator_functions = $validator_functions;
        } elseif ($validator_functions === null && isset($this->options[$name])) {
            $validator_functions = $this->options[$name]['validators'];
        } else {
            $validator_functions = [];
        }
        return $validator_functions;
    }

    /**
     * Validate all options
     * @param array $options
     * @return void
     */
    public function validateOptionsAll(array $options = []) : bool
    {
        /**
         * Validation all functions
         */
        $valid = true;
        foreach ($options as $name => $value) {
            if (isset($this->options[$name])) {
                $valid &= $this->validateOption($name, $value);
            }
        }
        /**
         * Return bool value
         */
        return $valid;
    }

    /**
     * Has option in array $options with $name
     * @param array $options
     * @param string $name
     * @return bool
     */
    public function hasOption(array $options = [], string $name)
    {
        return isset($options[$name]);
    }

    /**
     * Check options equal
     * @param array $options
     * @param string $name
     * @param $value
     * @param bool $district
     * @return bool
     */
    public function optionEqual(array $options = [], string $name, $value, bool $district = true)
    {
        return isset($options[$name]) && ($district ? $options[$name] === $value : $options[$name] == $value);
    }

    /**
     * Set filter name
     * @param string $name
     * @return void
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * Get filter name
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set options validators and configure filter
     * @return void
     */
    public function configure() : void
    {
        // $this->setOptionValidator('example', false, 'is_bool|is_null|empty');
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter(string $text, array $options = []) : string
    {
        $this->setOptionsAll($options);

        return $this->doFilter($text, $options);
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    abstract public function doFilter(string $text, array $options = []) : string;

}
