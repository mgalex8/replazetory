<?php
namespace App\Library\HtmlDomParser\Contracts;

class AbstractOptions
{

    /**
     * Options constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $parameters = get_object_vars($this);
        foreach($options as $key => $value) {
            if (isset($parameters[$key])) {
                $this->{$key} = $value;
            }
        }
    }

}
