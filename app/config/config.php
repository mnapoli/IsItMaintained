<?php

use Aura\Router\Router;
use Aura\Router\RouterFactory;
use Interop\Container\ContainerInterface;
use function DI\factory;
use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgRender;

return [
    'routes' => require __DIR__ . '/routes.php',

    Router::class => factory(function (ContainerInterface $c) {
        $router = (new RouterFactory())->newInstance();

        // Add the routes from the array config (Aura router doesn't seem to accept routes as array)
        $routes = $c->get('routes');
        foreach ($routes as $routeName => $route) {
            $router->add($routeName, $route['pattern'])
                ->addValues(['controller' => $route['controller']]);
        }

        return $router;
    }),

    Poser::class => factory(function () {
        return new Poser([new SvgRender()]);
    }),
];
