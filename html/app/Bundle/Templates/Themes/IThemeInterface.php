<?php
namespace App\Bundle\Templates\Themes;

/**
 * Interface IThemeInterface
 */
interface IThemeInterface
{

    /**
     * @return string
     */
    public function getPath() : string;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return void
     */
    public function activate() : void;

    /**
     * @return void
     */
    public function deactivate() : void;

    /**
     * @return bool
     */
    public function isActive() : bool;

}