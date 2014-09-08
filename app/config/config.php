<?php

use Aura\Router\Router;
use Aura\Router\RouterFactory;
use Interop\Container\ContainerInterface;
use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgRender;
use function DI\factory;

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

    Twig_Environment::class => factory(function () {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../src/Maintained/Application/View');
        return new Twig_Environment($loader);
    }),
];
