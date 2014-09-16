<?php

use Aura\Router\Router;
use DI\Container;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var Container $container */
$container = require __DIR__ . '/../app/container.php';

/** @var Router $router */
$router = $container->get(Router::class);

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = $router->match($url, $_SERVER);
if (! $route) {
    header('HTTP/1.0 404 Not Found');
    return false;
}
$requestParameters = $route->params;
$controller = $requestParameters['controller'];

// Handle the case where the controller is an invokable class
if (is_string($controller) && class_exists($controller)) {
    $controller = $container->make($controller);
}

// Dispatch
$container->call($controller, $requestParameters);
