<?php

use Aura\Router\Router;
use Aura\Router\RouterFactory;
use BlackBox\Adapter\MapAdapter;
use BlackBox\Backend\FileStorage;
use BlackBox\Backend\MultipleFileStorage;
use BlackBox\Transformer\JsonEncoder;
use BlackBox\Transformer\MapWithTransformers;
use BlackBox\Transformer\ObjectArrayMapper;
use BlackBox\Transformer\PhpSerializeEncoder;
use BlackBox\Transformer\StorageWithTransformers;
use DI\Container;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Github\Client;
use Github\HttpClient\CachedHttpClient;
use Interop\Container\ContainerInterface;
use Maintained\Application\Twig\TwigExtension;
use Maintained\Repository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PiwikTwigExtension\PiwikTwigExtension;
use Psr\Log\LoggerInterface;
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

    // Logger
    LoggerInterface::class => factory(function (ContainerInterface $c) {
        $logger = new Logger('main');
        $file = $c->get('directory.logs') . '/app.log';
        $logger->pushHandler(new StreamHandler($file, Logger::WARNING));
        return $logger;
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
        $backend = new StorageWithTransformers(
            new FileStorage($c->get('directory.data') . '/repositories.json')
        );
        $backend->addTransformer(new JsonEncoder(true));
        $storage = new MapWithTransformers(
            new MapAdapter($backend)
        );
        $storage->addTransformer(new ObjectArrayMapper(Repository::class));
        return $storage;
    }),
    'storage.statistics' => factory(function (ContainerInterface $c) {
        $storage = new MapWithTransformers(
            new MultipleFileStorage($c->get('directory.data') . '/statistics')
        );
        $storage->addTransformer(new PhpSerializeEncoder);
        return $storage;
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
