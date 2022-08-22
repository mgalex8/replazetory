<?php
namespace App\Bundle\YamlReplacerParser;

use App\Bundle\YamlReplacerParser\Interfaces\IYamlConfigFilter;

/**
 * Class ContentFiltrator
 */
class ContentFiltrator
{

    /**
     * @var array
     */
    protected $filters;

    /**
     * ContentFiltrator Constructor.
     */
    public function __construct(array $filters = [])
    {
        $this->setFiltersAll($filters);
    }

    /**
     * @param IYamlConfigFilter $filter
     * @param string|null $name
     * @return ContentFiltrator
     */
    public function setFilter(IYamlConfigFilter $filter, string $name = null) : ContentFiltrator
    {
        $name = $name ?: $filter->getName();
        $this->filters[$name] = $filter;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters() : array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @return void
     */
    public function setFiltersAll(array $filters = []) : void
    {
        foreach ($filters as $name => $filter) {
            $this->setFilter($filter, $name);
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function exists(string $name) : bool
    {
        return isset($this->filters[$name]);
    }

    /**
     * @param string $content
     * @param string $filter_name
     * @param array $filter_options
     * @return void
     * @throws \Exception
     */
    public function filter(string $content, string $filter_name, array $filter_options = []) : string
    {
        if (! $this->exists($filter_name)) {
            throw new \Exception(sprintf('Filter name `%s` not found in class %s', $filter_name, __CLASS__));
        }
        return $this->filters[$filter_name]->filter($content, $filter_options);
    }


}