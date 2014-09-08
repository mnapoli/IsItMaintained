<?php

use Aura\Router\Router;
use Aura\Router\RouterFactory;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Interop\Container\ContainerInterface;
use Maintained\Badge\BadgeGenerator;
use Maintained\Badge\BadgeProvider;
use Maintained\Badge\CachedBadgeProvider;
use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgRender;
use function DI\factory;
use function DI\link;
use function DI\object;

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

    Cache::class => object(FilesystemCache::class)
        ->constructor(__DIR__ . '/../../app/cache/badges'),

    BadgeProvider::class => object(CachedBadgeProvider::class)
        ->constructorParameter('wrapped', link(BadgeGenerator::class)),
];
