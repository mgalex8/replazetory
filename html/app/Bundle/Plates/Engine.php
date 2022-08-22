<?php
namespace App\Bundle\Plates;

use League\Plates\Engine as LeaguePlatesEngine;
use League\Plates\Template\Func;
use League\Plates\Template\Name;
use League\Plates\Template\Template;

class Engine extends LeaguePlatesEngine
{

    /**
     * @var string
     */
    protected $currentThemeName = '';

    /**
     * @param $name
     * @param array $data
     * @return mixed
     */
    public function render($name, array $data = array())
    {
        $name = $this->getCurrentTemplate($name);
        return parent::render($name, $data);
    }

    /**
     * @param string $name
     * @return void
     */
    public function setCurrentThemeName(string $name)
    {
        $this->currentThemeName = $name;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getCurrentTemplate(string $name)
    {
        return $this->currentThemeName ? $this->currentThemeName.'::'.$name : $name;
    }
//
//    /**
//     * Register a new template function.
//     * @param  string   $name;
//     * @param  callback $callback;
//     * @return \League\Plates\Engine
//     */
//    public function registerFunction($name, $callback)
//    {
//        return parent::registerFunction($this->getCurrentTemplate($name), $callback);
//    }
//
//    /**
//     * Remove a template function.
//     * @param  string $name;
//     * @return Engine
//     */
//    public function dropFunction($name)
//    {
//        return parent::dropFunction($this->getCurrentTemplate($name));
//    }
//
//    /**
//     * Get a template function.
//     * @param  string $name
//     * @return Func
//     */
//    public function getFunction($name)
//    {
//        return parent::getFunction($this->getCurrentTemplate($name));
//    }

    /**
     * Check if a template function exists.
     * @param  string  $name
     * @return boolean
     */
    public function doesFunctionExist($name)
    {
        return parent::doesFunctionExist($this->getCurrentTemplate($name));
    }

}