<?php
namespace App\Library\DomTemplate\Replacer\Traits;

/**
 * ReplacerDelimiters trait
 */
trait ReplacerDelimiters
{

    /**
     * Start symbol delimiter in replacer template
     * @var string
     */
    protected $start_delimiter = '*';

    /**
     * End symbol delimiter in replacer template
     * @var string
     */
    protected $end_delimiter = '*';

    /**
     * Index delimiter in template
     * @var string
     */
    protected $index_delimiter = '#';

    /**
     * Set all delimiters for template
     * @param string $start_delimiter
     * @param string $end_delimiter
     * @param string $index_delimiter
     * @return void
     */
    public function setDelimiters(string $start_delimiter = '*', string $end_delimiter = '*', string $index_delimiter = '#,') : void {
        $this->start_delimiter = $start_delimiter;
        $this->end_delimiter = $end_delimiter;
        $this->index_delimiter = $index_delimiter;
    }

    /**
     * Set parameter start_delimiter
     * @param string $start_delimiter
     */
    public function setStartDelimiter(string $start_delimiter): void {
        $this->start_delimiter = $start_delimiter;
    }

    /**
     * Set parameter end_delimiter
     * @param string $end_delimiter
     */
    public function setEndDelimiter(string $end_delimiter): void {
        $this->end_delimiter = $end_delimiter;
    }

    /**
     * Set parameter index_delimiter
     * @param string $index_delimiter
     */
    public function setIndexDelimiter(string $index_delimiter): void {
        $this->index_delimiter = $index_delimiter;
    }

    /**
     * Get parameter start_delimiter
     * repeat symbols if parameter $repeat > 0
     * ecranized with '\' if parameter $ecranized is true
     * @param int $repeat
     * @param bool $ecranized
     * @return string
     */
    public function getStartDelimiter(int $repeat = 0, bool $ecranized = false): string {
        return $this->ecranizeDelimiter('start_delimiter', $repeat, $ecranized);
    }

    /**
     * Get parameter start_delimiter
     * repeat symbols if parameter $repeat > 0
     * ecranized with '\' if parameter $ecranized is true
     * @param int $repeat
     * @param bool $ecranized
     * @return string
     */
    public function getEndDelimiter(int $repeat = 0, bool $ecranized = false): string {
        return $this->ecranizeDelimiter('end_delimiter', $repeat, $ecranized);
    }

    /**
     * Get parameter start_delimiter
     * repeat symbols if parameter $repeat > 0
     * ecranized with '\' if parameter $ecranized is true
     * @param int $repeat
     * @param bool $ecranized
     * @return string
     */
    public function getIndexDelimiter(int $repeat = 0, bool $ecranized = false): string {
        return $this->ecranizeDelimiter('index_delimiter', $repeat, $ecranized);
    }

    /**
     * Ecranize parameter $parameter_name
     * with repeat count $repeat > 0 and ecranized '\' if $ecranized = true
     * @param string $parameter_name
     * @param int $repeat
     * @param bool $ecranized
     * @return string
     */
    protected function ecranizeDelimiter(string $parameter_name, int $repeat = 0, bool $ecranized = false): string {
        $repeat = $repeat < 0 ? 0 : min($repeat, 3);
        return str_repeat($ecranized ? "\\" . $this->{$parameter_name} : $this->{$parameter_name}, $repeat);
    }

}
