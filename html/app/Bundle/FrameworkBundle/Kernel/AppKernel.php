<?php
namespace App\Bundle\FrameworkBundle\Kernel;

use App\Bundle\FrameworkBundle\Kernel\ConfigFileExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader as ContainerPhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Loader\PhpFileLoader as RoutingPhpFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;

class AppKernel extends Kernel
{

    public function __construct(string $environment, bool $debug)
    {
        if (!defined('ABS_PATH')) {
            define('ABS_PATH', __DIR__);
        }

        $this->container = $this->buildContainer();
        $this->getContainer()->set('http_kernel', $this->buildHttpKernel());
        $this->buildDefaultContainers();
        parent::__construct($environment, $debug);
    }

    /**
     * @return void
     */
    protected function buildDefaultContainers()
    {
        $this->container->set('request', Request::createFromGlobals());
        $this->container->set('request_stack', new RequestStack());
        $this->container->set('kernel.controller_resolver', new ControllerResolver());
        $this->container->set('kernel.argument_resolver', new ArgumentResolver());
    }

    /**
     * @param Request|null $request
     * @param RequestStack|null $requestStack
     * @param ControllerResolver|null $controllerResolver
     * @param ArgumentResolver|null $argumentResolver
     * @return HttpKernel
     * @throws \Exception
     */
    public function buildHttpKernel(?Request $request = null, ?RequestStack $requestStack = null, ?ControllerResolver $controllerResolver = null, ?ArgumentResolver $argumentResolver = null)
    {
        // create routes
        [ $routes, $requestContext, $urlGenerator ] = require $this->getProjectDir().'/routes/web.php';

        // create the Request object
        $request = $request ?: ($this->container->has('request') ? $this->container->get('request') : Request::createFromGlobals());
        $requestStack = $requestStack ?: ($this->container->has('request_stack') ? $this->container->get('request_stack') : new RequestStack());

        $matcher = new UrlMatcher($routes, $requestContext);
        if ($this->container->has('router_listener')) {
            $routerListener = $this->container->get('router_listener');
        } else {
            $routerListener = new RouterListener($matcher, $requestStack);
            $this->container->set('router_listener', $routerListener);
        }

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($routerListener);
        // ... add some event listeners
        $this->container->set('kernel.event_dispatcher', $dispatcher);

        // create your controller and argument resolvers
        $controllerResolver = $controllerResolver ?: ($this->container->has('kernel.controller_resolver') ? $this->container->get('kernel.controller_resolver') : new ControllerResolver());
        $argumentResolver = $argumentResolver ?: ($this->container->has('kernel.argument_resolver') ? $this->container->get('kernel.argument_resolver') : new ArgumentResolver());

        // instantiate the kernel
        return new HttpKernel($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
    }

    /**
     * @return array|\Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     */
    public function registerBundles()
    {
        dd(1);
        $bundles = array(
            new App\Bundle\FrameworkBundle\FrameworkBundle(),
//            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
//            new Symfony\Bundle\TwigBundle\TwigBundle(),
//            new Symfony\Bundle\MonologBundle\MonologBundle(),
//            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
//            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
//            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
//            new JMS\AopBundle\JMSAopBundle(),
//            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
//            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
//            new JMS\SerializerBundle\JMSSerializerBundle(),
//            new Helios\BlogBundle\HeliosBlogBundle(),
//            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
//            new Helios\UserBundle\HeliosUserBundle(),
//            new FOS\UserBundle\FOSUserBundle(),
//            new FOS\ElasticaBundle\FOSElasticaBundle(),
//            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
//            new Helios\ManagerBundle\HeliosManagerBundle(),
//            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
////new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
//            new Oneup\UploaderBundle\OneupUploaderBundle(),
//            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
//            new Sonata\AdminBundle\SonataAdminBundle(),
//            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
//            new Sonata\BlockBundle\SonataBlockBundle(),
//            new Sonata\CoreBundle\SonataCoreBundle(),
//            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
//            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
//            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
//            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
//            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
//            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
//            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
//            $bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
        }

        return $bundles;
    }

//    /**
//     * @param LoaderInterface $loader
//     * @return void
//     */
//    public function registerContainerConfiguration(LoaderInterface $loader)
//    {
////         $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
//    }

    /**
     * Configures the container.
     *
     * You can register extensions:
     *
     *     $container->extension('framework', [
     *         'secret' => '%secret%'
     *     ]);
     *
     * Or services:
     *
     *     $container->services()->set('halloween', 'FooBundle\HalloweenProvider');
     *
     * Or parameters:
     *
     *     $container->parameters()->set('halloween', 'lot of fun');
     */
    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $configDir = $this->getConfigDir();

        $container->import($configDir . '/{packages}/*.{php,yaml}');
        $container->import($configDir . '/{packages}/' . $this->environment . '/*.{php,yaml}');

        if (is_file($configDir . '/services.yaml')) {
            $container->import($configDir . '/services.yaml');
            $container->import($configDir . '/{services}_' . $this->environment . '.yaml');
        } else {
            $container->import($configDir . '/{services}.php');
        }
    }

    /**
     * Adds or imports routes into your application.
     *
     *     $routes->import($this->getConfigDir().'/*.{yaml,php}');
     *     $routes
     *         ->add('admin_dashboard', '/admin')
     *         ->controller('App\Controller\AdminController::dashboard')
     *     ;
     */
    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $configDir = $this->getConfigDir();

        $routes->import($configDir . '/{routes}/' . $this->environment . '/*.{php,yaml}');
        $routes->import($configDir . '/{routes}/*.{php,yaml}');

        if (is_file($configDir . '/routes.yaml')) {
            $routes->import($configDir . '/routes.yaml');
        } else {
            $routes->import($configDir . '/{routes}.php');
        }

        if (false !== ($fileName = (new \ReflectionObject($this))->getFileName())) {
            $routes->import($fileName, 'annotation');
        }
    }

    /**
     * Gets the path to the configuration directory.
     */
    private function getConfigDir(): string
    {
        return $this->getProjectDir() . '/config';
    }

    /**
     * Gets the path to the bundles configuration file.
     */
    private function getBundlesPath(): string
    {
        return $this->getConfigDir() . '/bundles.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        if (isset($_SERVER['APP_CACHE_DIR'])) {
            return $_SERVER['APP_CACHE_DIR'] . '/' . $this->environment;
        }

        return parent::getCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return $_SERVER['APP_LOG_DIR'] ?? parent::getLogDir();
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function registerBundles(): iterable
//    {
//        $contents = require $this->getBundlesPath();
//        foreach ($contents as $class => $envs) {
//            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
//                yield new $class();
//            }
//        }
//    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
//            $configFileExtension = new ConfigFileExtension();
//            $extension = $configFileExtension->load(['path' => $this->getConfigDir().'/packages', 'filename' => 'framework.yaml'], $container);
//            $container->registerExtension($extension);
//
//            $container->loadFromExtension('framework', [
//                'router' => [
//                    'resource' => 'kernel::loadRoutes',
//                    'type' => 'service',
//                ],
//            ]);

            $this->loadRoutes($loader);

            $kernelClass = str_contains(static::class, "@anonymous\0") ? parent::class : static::class;

            if (!$container->hasDefinition('kernel')) {
                $container->register('kernel', $kernelClass)
                    ->addTag('controller.service_arguments')
                    ->setAutoconfigured(true)
                    ->setSynthetic(true)
                    ->setPublic(true);
            }

            $kernelDefinition = $container->getDefinition('kernel');
            $kernelDefinition->addTag('routing.route_loader');

            $container->addObjectResource($this);
            $container->fileExists($this->getBundlesPath());

            $configureContainer = new \ReflectionMethod($this, 'configureContainer');
            $configuratorClass = $configureContainer->getNumberOfParameters() > 0 && ($type = $configureContainer->getParameters()[0]->getType()) instanceof \ReflectionNamedType && !$type->isBuiltin() ? $type->getName() : null;

            if ($configuratorClass && !is_a(ContainerConfigurator::class, $configuratorClass, true)) {
                $configureContainer->getClosure($this)($container, $loader);

                return;
            }

            $file = (new \ReflectionObject($this))->getFileName();
            /* @var ContainerPhpFileLoader $kernelLoader */
            $kernelLoader = $loader->getResolver()->resolve($file);
            $kernelLoader->setCurrentDir(\dirname($file));
            $var = \Closure::bind(function &() {
                return $this->instanceof;
            }, $kernelLoader, $kernelLoader)();
            $instanceof = &$var;

            $valuePreProcessor = AbstractConfigurator::$valuePreProcessor;
            AbstractConfigurator::$valuePreProcessor = function ($value) {
                return $this === $value ? new Reference('kernel') : $value;
            };

            try {
                $configureContainer->getClosure($this)(new ContainerConfigurator($container, $kernelLoader, $instanceof, $file, $file, $this->getEnvironment()), $loader, $container);
            } finally {
                $instanceof = [];
                $kernelLoader->registerAliasesForSinglyImplementedInterfaces();
                AbstractConfigurator::$valuePreProcessor = $valuePreProcessor;
            }

            $container->setAlias($kernelClass, 'kernel')->setPublic(true);
        });
    }

    /**
     * @internal
     */
    public function loadRoutes(LoaderInterface $loader): RouteCollection
    {
        $file = (new \ReflectionObject($this))->getFileName();

        if (! $loader instanceof RoutingPhpFileLoader) {
            $loader = new RoutingPhpFileLoader(new FileLocator($this));
        }

        [ $collection, $requestContext, $urlGenerator ] = require $this->getProjectDir().'/routes/web.php';

////        $kernelLoader = $loader->getResolver()->resolve($file, 'php');
////        $kernelLoader->setCurrentDir(\dirname($file));
//
//        /* @var RoutingPhpFileLoader $kernelLoader */
//        $collection = new RouteCollection();
//        $configureRoutes = new \ReflectionMethod($this, 'configureRoutes');
//        $configureRoutes->getClosure($this)(new RoutingConfigurator($collection, $kernelLoader, $file, $file, $this->getEnvironment()));
//
//        foreach ($collection as $route) {
//            $controller = $route->getDefault('_controller');
//
//            if (\is_array($controller) && [0, 1] === array_keys($controller) && $this === $controller[0]) {
//                $route->setDefault('_controller', ['kernel', $controller[1]]);
//            } elseif ($controller instanceof \Closure && $this === ($r = new \ReflectionFunction($controller))->getClosureThis() && !str_contains($r->name, '{closure}')) {
//                $route->setDefault('_controller', ['kernel', $r->name]);
//            }
//        }

        return $collection;
    }

    public function test()
    {
        dd($this->getContainer());
    }

}