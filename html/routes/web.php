<?php
/**
 * ===============================================================================================================
 * Create route collection
 * ===============================================================================================================
 */
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();
$requestContext = new RequestContext();
$urlGenerator = new UrlGenerator($routes, $requestContext);

/**
 * ===============================================================================================================
 * Setup routes for web pages
 * ===============================================================================================================
 */

/** Home routes */
$routes->add('home',           new Route('/home',                 [ '_controller' => [ new App\Controller\Home\HomeController(), 'index' ] ] ) );
$routes->add('home.text',      new Route('/home/{text}',          [ '_controller' => [ new App\Controller\Home\HomeController(), 'gettext' ] ] ) );

/** Content routes */
$routes->add('replacer.file.finder',        new Route('/replacer/file/finder', [ '_controller' => [ new App\Controller\Replacer\FileFinderController(), 'index' ] ] ) );
$routes->add('replacer.file.finder.files',  new Route('/replacer/file/finder/files', [ '_controller' => [ new App\Controller\Replacer\FileFinderController(), 'files' ] ] ) );

/** Content routes */
$routes->add('content.insert_db',           new Route('/content/insert_db', [ '_controller' => [ new App\Controller\Html\ContentController(), 'insert_db' ], [ 'url' => '' ] ] ) );
$routes->add('content.original',            new Route('/content/original',  [ '_controller' => [ new App\Controller\Html\ContentController(), 'original' ], [ 'url' => '' ] ] ) );
$routes->add('content.replaced',            new Route('/content/replaced',  [ '_controller' => [ new App\Controller\Html\ContentController(), 'replaced' ], [ 'url' => '' ] ] ) );

/** Messages routes */
$routes->add('messages.new',   new Route('/messages/new/{id}',    [ '_controller' => 'App\Controller\Messages\MessageController::new', 'id' => '.+' ] ) );
$routes->add('messages.dep',   new Route('/messages/dep/{id}',    [ '_controller' => 'App\Controller\Messages\MessageController::dep', 'id' => '.+', 'messageGenerator' => new \App\Service\MessageGenerator() ] ) );

/** Redirect routes */
//$routes->add('trailingSlash',  new Route('/trailing/{page}{slug_cat}{trailingSlash}{slug_cat}', [
//        '_controller' => 'App\Controller\Html\ContentController::index',
//        'page' => 1,
//        'trailingSlash' => "/",
//    ], [
//        'trailingSlash' => '[/]{0,1}',
//        'slug_cat' => '.*$',
//        'page' => '\d+',
//    ] ) );

/** Admin routes */
$routes->add('admin', new Route('/admin', [
    '_controller' => [ new App\Bundle\FrameworkBundle\Controller\RedirectController($urlGenerator), 'redirectAction' ],
    'route' => 'home',
    'permanent' => true,      // make a permanent redirection...
    'keepQueryParams' => true,  // keep the original query string parameters
]));


/**
 * ===============================================================================================================
 * Return RouteCollection
 * ===============================================================================================================
 */

return [ $routes, $requestContext, $urlGenerator ];