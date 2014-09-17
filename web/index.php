<?php

use Aura\Router\Router;
use DI\Container;
use Maintained\Application\Controller\Error404Controller;
use Maintained\Application\Controller\MaintenanceController;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var Container $container */
$container = require __DIR__ . '/../app/container.php';

if ($container->get('maintenance')) {
    $controller = MaintenanceController::class;
    $requestParameters = [];
} else {
    /** @var Router $router */
    $router = $container->get(Router::class);

    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $route = $router->match($url, $_SERVER);
    if ($route) {
        $requestParameters = $route->params;
        $controller = $requestParameters['controller'];
    } else {
        if (php_sapi_name() === 'cli-server') {
            return false;
        }

        $controller = Error404Controller::class;
        $requestParameters = [];
    }
}

// Handle the case where the controller is an invokable class
if (is_string($controller) && class_exists($controller)) {
    $controller = $container->make($controller);
}

// Dispatch
$container->call($controller, $requestParameters);
