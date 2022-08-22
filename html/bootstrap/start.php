<?php
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\EventListener\ErrorListener as HttpKernelErrorListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;

require_once __DIR__.'/../vendor/autoload.php';

// create routes
[ $routes, $requestContext, $urlGenerator ] = require __DIR__.'/../routes/web.php';

// create the Request object
$request = Request::createFromGlobals();
$requestStack = new RequestStack();

$matcher = new UrlMatcher($routes, $requestContext);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));
$dispatcher->addSubscriber(new HttpKernelErrorListener('\App\Error\Controller\Calendar\CalendarControllerError::exception'));
// ... add some event listeners

// create your controller and argument resolvers
$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

// instantiate the kernel
//$kernel = new HttpKernel($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
$kernel = new \App\Bundle\FrameworkBundle\Kernel\AppKernel('local', true);

return [
    $kernel, $request
];