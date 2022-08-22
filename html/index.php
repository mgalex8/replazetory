<?php
require_once __DIR__.'/vendor/autoload.php';

//list($kernel, $request) = require __DIR__.'/bootstrap/start.php';

if (!defined('ABS_PATH')) define('ABS_PATH', __DIR__);

$kernel = new \App\Bundle\FrameworkBundle\Kernel\AppKernel('local', true);

$request = $kernel->getContainer()->get('request');

// actually execute the kernel, which turns the request into a response
// by dispatching events, calling a controller, and returning the response
$response = $kernel->handle($request);

// send the headers and echo the content
$response->send();

// trigger the kernel.terminate event
$kernel->terminate($request, $response);
