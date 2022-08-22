<?php
namespace App\Bundle\Templates\Themes;

/**
 * Class Theme
 */
class Theme implements IThemeInterface
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var
     */
    protected $name;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var bool
     */
    protected $active;


    /**
     * @param string $name
     * @param string $path
     * @param array $additional_parameters
     */
    public function __construct(string $name, string $path, array $parameters = [], bool $active = true)
    {
        $this->name = $name;
        $this->path = $path;
        $this->active = $active;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return void
     */
    public function activate() : void
    {
        $this->active = true;
    }

    /**
     * @return void
     */
    public function deactivate() : void
    {
        $this->active = false;
    }

    /**
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function checkPath() : bool
    {
        // code...
        return true;
    }

}