<?php
namespace App\Bundle\Templates\Views;

use App\Bundle\Templates\Themes\IThemeInterface;

/**
 * Interface IViewInterface
 */
interface IViewInterface
{
    /**
     * @param string $view
     * @param array $args
     * @param IThemeInterface|null $theme
     * @return string
     */
    public function render(string $view = '', array $args = [], ?IThemeInterface $theme = null) : string;
}