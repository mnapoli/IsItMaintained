<?php

use Aura\Router\Router;
use Aura\Router\RouterFactory;
use DI\Container;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Github\Client;
use Github\HttpClient\CachedHttpClient;
use Interop\Container\ContainerInterface;
use Maintained\Application\Command\ClearCacheCommand;
use Maintained\Application\Command\ShowStatisticsCommand;
use Maintained\Application\Twig\TwigExtension;
use Maintained\Statistics\CachedStatisticsProvider;
use Maintained\Statistics\StatisticsComputer;
use Maintained\Statistics\StatisticsProvider;
use Maintained\Statistics\StatisticsProviderLogger;
use Maintained\Storage\JsonFileStorage;
use Maintained\Storage\Storage;
use PiwikTwigExtension\PiwikTwigExtension;
use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgRender;
use function DI\factory;
use function DI\link;
use function DI\object;

return [
    ContainerInterface::class => link(Container::class),

    'baseUrl' => 'http://isitmaintained.com',

    'directory.cache' => __DIR__ . '/../../app/cache',
    'directory.data' => __DIR__ . '/../../app/data',

    // Routing
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

    // Piwik tracking
    'piwik.enabled' => false,
    'piwik.host' => null,
    'piwik.site_id' => null,

    // Cache
    Cache::class => factory(function (ContainerInterface $c) {
        $cache = new FilesystemCache($c->get('directory.cache') . '/app');
        $cache->setNamespace('Maintained');

        return $cache;
    }),

    // GitHub API
    'github.auth_token' => null,
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

    Storage::class => object(JsonFileStorage::class)
        ->constructorParameter('directory', link('directory.data')),

    StatisticsProvider::class => object(CachedStatisticsProvider::class)
        ->constructorParameter('wrapped', link(StatisticsProviderLogger::class)),
    StatisticsProviderLogger::class => object()
        ->constructorParameter('wrapped', link(StatisticsComputer::class)),

    ClearCacheCommand::class => object()
        ->lazy()
        ->constructorParameter('cacheDirectory', link('directory.cache')),
    ShowStatisticsCommand::class => object()
        ->lazy(),
];
