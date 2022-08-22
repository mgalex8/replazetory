<?php
namespace App\Bundle\FrameworkBundle\Kernel;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ConfigFileExtension implements ExtensionInterface
{

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator($configs['path'])
        );
        $loader->load($configs['filename']);
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string
     */
    public function getNamespace()
    {
        return 'framework';
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string|false
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string
     */
    public function getAlias()
    {
        return '';
    }
}