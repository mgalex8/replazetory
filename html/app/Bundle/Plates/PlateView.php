<?php
namespace App\Bundle\Plates;

use App\Bundle\Plates\Engine;
use League\Plates\Extension\Asset;
use League\Plates\Extension\URI;

class PlateView
{

    /**
     * @var League\Plates\Engine
     */
    protected static $engine;

    /**
     * @var bool
     */
    protected static $is_conf = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::configure();
    }

    /**
     * @return void
     */
    protected static function configure()
    {
        self::$engine = new Engine(ABS_PATH.'/templates');

        // set file extension
        self::$engine->setFileExtension('plate.php');
        // Load URI extension using global variable
        self::$engine->loadExtension(new URI($_SERVER['PATH_INFO']));
        // load assets
        self::$engine->loadExtension(new Asset(ABS_PATH, true));

        // set theme folder
        self::$engine->addFolder('common', ABS_PATH.'/templates/common', true);
        self::$engine->addFolder('basis', ABS_PATH.'/templates/themes/basis', true);
        self::$engine->addFolder('reddy', ABS_PATH.'/templates/themes/reddy', true);
        self::$engine->setCurrentThemeName('common');

        self::$is_conf = true;
    }

    /**
     * @param $name
     * @param array $data
     * @return mixed
     */
    public static function render($name, array $data = array())
    {
        if (! self::$is_conf) {
            self::configure();
        }
        return self::$engine->render($name, [ 'args' => (object) $data ]);
    }

//    /**
//     * @return array
//     */
//    public function routeAction()
//    {
//        /** @var Router $router */
//        $router = $this->get('router');
//        $routes = $router->getRouteCollection();
//
//        foreach ($routes as $route) {
//            $this->convertController($route);
//        }
//
//        return [
//            'routes' => $routes
//        ];
//    }
//
//    /**
//     * @param \Symfony\Component\Routing\Route $route
//     * @return void
//     */
//    private function convertController(\Symfony\Component\Routing\Route $route)
//    {
//        $nameParser = $this->get('controller_name_converter');
//        if ($route->hasDefault('_controller')) {
//            try {
//                $route->setDefault('_controller', $nameParser->build($route->getDefault('_controller')));
//            } catch (\InvalidArgumentException $e) {
//            }
//        }
//    }

}