<?php

use Aura\Router\Router;
use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../app/config/config.php');
$container = $builder->build();

/** @var Router $router */
$router = $container->get(Router::class);

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestParameters = $router->match($url, $_SERVER)->params;
$controller = $requestParameters['controller'];

// Handle the case where the controller is an invokable class
if (is_string($controller) && class_exists($controller)) {
    $controller = $container->make($controller);
}

// Dispatch
$container->call($controller, $requestParameters);
