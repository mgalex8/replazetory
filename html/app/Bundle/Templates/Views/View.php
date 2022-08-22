<?php
namespace App\Bundle\Templates\Views;

use App\Bundle\Templates\Themes\IThemeInterface;
use App\Bundle\Templates\Themes\Theme;
use App\Bundle\TemplateThemes\ITemplateThemeInterface;

/**
 * Class HtmlView
 */
class View implements IViewInterface, \Stringable
{

    /**
     * @var string
     */
    protected $view;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $themes = [];

    /**
     * @var array
     */
    protected $themePaths = [];

    /**
     * @var string|null
     */
    protected $currentThemeName = null;

    /**
     * @var null
     */
    protected $templateBasePath = null;

    /**
     * @var string
     */
    protected $fileExtension = '.html.php';

    /**
     * @param string $view
     * @param array $args
     * @param IThemeInterface|null $theme
     * @return void
     * @throws \Exception
     */
    public function __construct(string $view = '', array $args = [], ?IThemeInterface $theme = null)
    {
        $this->view = $view;
        $this->args = $args;

        $this->setTemplateBasePath(ABS_PATH.\DIRECTORY_SEPARATOR.'templates');
        $this->setThemeCommon(ABS_PATH.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'common');
        $this->setCurrentTheme('_common');

        if ($theme) {
            $this->addTheme($theme, true);
        }
    }

    /**
     * Render view
     * @param string $view
     * @param array $args
     * @param IThemeInterface|null $theme
     * @return string
     * @throws \Exception
     */
    public function render(string $view = '', array $args = [], ?IThemeInterface $theme = null) : string
    {
        $viewPath = null;

        /**
         * if parameter $view is absolute path and file exists
         * then set $viewPath as $view
         */
        if (file_exists($view)) {
            $viewPath = $view;

            if (! preg_match('/'.quotemeta($this->getFileExtension()).'$/', $viewPath)) {
                throw new \Exception(sprintf('Template path %s found but not accessible then file extension must be %s', $viewPath, $this->fileExtension));
            }
        }
        /**
         * if found current template Theme
         * then try set current theme
         * else try set absolute path from relative path
         */
        else {
            $currentTheme = $theme ?: $this->getCurrentTheme();
            if ($currentTheme) {
                $viewPath = rtrim($currentTheme->getPath(), \DIRECTORY_SEPARATOR).\DIRECTORY_SEPARATOR.ltrim($view, \DIRECTORY_SEPARATOR);
            } else {
                $viewPath = ABS_PATH.trim($view, \DIRECTORY_SEPARATOR).'.'.$this->getFileExtension();
            }
        }

        $viewPath = preg_replace('/'.quotemeta($this->getFileExtension()).'$/', '', $viewPath) . '.'.$this->getFileExtension();
        if (! file_exists($viewPath)) {
            throw new \Exception(sprintf('Template path %s is not found ', $viewPath));
        }
//        dd($viewPath);

        $view = $this;

        ob_start();

        include $viewPath;

        return ob_get_clean();
    }

    /**
     * Add new Template Theme
     * @param string $name
     * @param string $absolutePath
     * @return View
     */
    public function addTheme(IThemeInterface $theme, bool $setCurrent = true) : View
    {
        $this->themes[$theme->getName()] = $theme;
        $this->themesPaths[$theme->getPath()] = $theme->getName();

        if ($setCurrent) {
            $this->currentThemeName = $theme->getName();
        }

        return $this;
    }

    /**
     * Get Template Theme by name
     * @param string $name
     * @return View
     * @throws \Exception
     */
    public function getTheme(string $name) : IThemeInterface
    {
        if (! $this->existTheme($name)) {
            throw new \Exception(sprintf('Theme name `%s` is not includes in class %s', $name, __CLASS__));
        }
        return $this->themes[$name];
    }

    /**
     * Get all Template Themes in this class
     * @return array
     */
    public function getThemesAll() : array
    {
        return $this->themes;
    }

    /**
     * Get current Template Theme
     * @return Theme|null
     * @throws \Exception
     */
    public function getCurrentTheme() : ?Theme
    {
        return !is_null($this->current_theme_name) ? $this->getTheme($this->current_theme_name) : null;
    }

    /**
     * @param IThemeInterface|string $theme
     * @return View
     * @throws \Exception
     */
    public function setCurrentTheme($theme) : View
    {
        if ($theme instanceof IThemeInterface) {
            $this->addTheme($theme, true);
        } elseif (is_string($theme)) {
            $this->current_theme_name = $this->getTheme($theme)->getName();
        } else {
            throw new \Exception(sprintf(
                'Argument 0 name $theme in method %s must be string for themes name or type of %s, but given %s',
                __METHOD__,
                IThemeInterface::class,
                is_object($theme) ? get_class($theme) : gettype($theme)
            ));
        }

        return $this;
    }

    /**
     * @param string $name
     * @return View
     * @throws \Exception
     */
    public function removeTheme(string $name) : View
    {
        if (! $this->existTheme($name)) {
            throw new \Exception(sprintf('Theme name `%s` is not includes in class %s', $name, __CLASS__));
        }
        unset($this->themes[$name]);

        return $this;
    }

    /**
     * @return View
     */
    public function disableTheme($theme) : View
    {
        // code...
        return $this;
    }

    /**
     * @return View
     */
    public function enableTheme($theme) : View
    {
        // code...
        return $this;
    }

    /**
     * @return View
     */
    public function disableThemeTemporarily($theme) : View
    {
        // code...
        return $this;
    }

    /**
     * Set template Theme with name `_common`
     * @param string|null $common_theme_path
     * @return View
     */
    public function setThemeCommon(?string $commonThemePath = null) : View
    {
        if ($commonThemePath && file_exists($commonThemePath) && is_dir($commonThemePath)) {
            $this->addTheme(new Theme('_common', $commonThemePath));
        } elseif ($this->getTemplateBasePath() !== null) {
            $this->addTheme(new Theme('_common', rtrim($this->getTemplateBasePath(), \DIRECTORY_SEPARATOR).\DIRECTORY_SEPARATOR.'common'));
        }

        return $this;
    }

    /**
     * Check if exists template Theme with name $name
     * @param string $name
     * @return bool
     */
    public function existTheme(string $name) : bool
    {
        return isset($this->themes[$name]);
    }

    /**
     * Check if exists template Theme with path $path
     * @param string $path
     * @return bool
     */
    public function existThemePath(string $path) : bool
    {
        return isset($this->themesPaths[$path]);
    }

    /**
     * Set default template base path
     * @param string $templateBasePath     Template path (directory only)
     * @return View
     */
    public function setTemplateBasePath(string $templateBasePath) : View
    {
        if (! file_exists($templateBasePath)) {
            throw new \Exception(sprintf('Path not found %s for argument 0 in method %s', $templateBasePath, __METHOD__));
        } elseif (! is_dir($templateBasePath)) {
            throw new \Exception(sprintf('Path %s is not directory for argument 0 in method %s', $templateBasePath, __METHOD__));
        }

        /** set templateBasePath */
        $this->templateBasePath = $templateBasePath;

        return $this;
    }

    /**
     * @param string $templatePath
     * @return void
     */
    public function getTemplateBasePath() : ?string
    {
        return $this->templateBasePath;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function renderCurrent() : ?string
    {
        if ($this->currentThemeName !== null) {
            return $this->render($this->view, $this->args, $this->getCurrentTheme());
        } else {
            return $this->render($this->view, $this->args);
        }
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView(string $view): void
    {
        $this->view = $view;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @return string|null
     */
    public function getCurrentThemeName(): ?string
    {
        return $this->currentThemeName;
    }

    /**
     * @param string|null $currentThemeName
     */
    public function setCurrentThemeName(?string $currentThemeName): void
    {
        $this->currentThemeName = $currentThemeName;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * @param string $fileExtension
     */
    public function setFileExtension(string $fileExtension): void
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __invoke() : string
    {
        return $this->renderCurrent();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString() : string
    {
        return $this->renderCurrent();
    }

}