<?php

use Aura\Router\Router;
use Aura\Router\RouterFactory;
use BlackBox\Adapter\FileStorage;
use BlackBox\Transformer\ArrayMapAdapter;
use BlackBox\Transformer\JsonEncoder;
use BlackBox\Transformer\ObjectArrayMapper;
use DI\Container;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Github\Client;
use Github\HttpClient\CachedHttpClient;
use Interop\Container\ContainerInterface;
use Maintained\Application\Twig\TwigExtension;
use Maintained\Repository;
use PiwikTwigExtension\PiwikTwigExtension;
use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgRender;
use function DI\factory;
use function DI\link;
use function DI\object;

return [

    ContainerInterface::class => link(Container::class),

    // Routing
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

    // Badge generator
    Poser::class => object()
        ->constructor(link(SvgRender::class)),

    // Twig
    Twig_Environment::class => factory(function (ContainerInterface $c) {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../src/Maintained/Application/View');
        $twig = new Twig_Environment($loader);

        $twig->addExtension($c->get(TwigExtension::class));
        $twig->addExtension($c->get(PiwikTwigExtension::class));

        return $twig;
    }),
    PiwikTwigExtension::class => object()
        ->constructor(link('piwik.host'), link('piwik.site_id'), link('piwik.enabled')),

    // Cache
    Cache::class => factory(function (ContainerInterface $c) {
        $cache = new FilesystemCache($c->get('directory.cache') . '/app');
        $cache->setNamespace('Maintained');

        return $cache;
    }),

    'storage.repositories' => factory(function (ContainerInterface $c) {
        return new ObjectArrayMapper(
            new ArrayMapAdapter(
                new JsonEncoder(
                    new FileStorage($c->get('directory.data') . '/repositories.json')
                )
            ),
            Repository::class
        );
    }),

    // GitHub API
    Client::class => factory(function (ContainerInterface $c) {
        $cacheDirectory = $c->get('directory.cache') . '/github';

        $client = new Client(
            new CachedHttpClient(['cache_dir' => $cacheDirectory])
        );

        $authToken = $c->get('github.auth_token');
        if ($authToken) {
            $client->authenticate($authToken, null, Client::AUTH_HTTP_TOKEN);
        }

        return $client;
    }),

];
