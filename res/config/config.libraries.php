<?php

use BlackBox\Adapter\MapAdapter;
use BlackBox\Backend\FileStorage;
use BlackBox\Backend\MultipleFileStorage;
use BlackBox\Transformer\JsonEncoder;
use BlackBox\Transformer\MapWithTransformers;
use BlackBox\Transformer\ObjectArrayMapper;
use BlackBox\Transformer\PhpSerializeEncoder;
use BlackBox\Transformer\StorageWithTransformers;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Github\Client;
use Github\HttpClient\CachedHttpClient;
use Interop\Container\ContainerInterface;
use Maintained\Application\Twig\TwigExtension;
use Maintained\Repository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use PUGX\Poser\Poser;
use PUGX\Poser\Render\SvgFlatRender;
use function DI\add;
use function DI\get;
use function DI\factory;
use function DI\object;

return [

    // Logger
    LoggerInterface::class => function (ContainerInterface $c) {
        $logger = new Logger('main');
        $file = $c->get('directory.logs') . '/app.log';
        $logger->pushHandler(new StreamHandler($file, Logger::WARNING));
        return $logger;
    },

    // Badge generator
    Poser::class => object()
        ->constructor(get(SvgFlatRender::class)),

    // Twig
    'twig.extensions' => add([
        get(TwigExtension::class),
    ]),

    // Cache
    Cache::class => function (ContainerInterface $c) {
        $cache = new FilesystemCache($c->get('directory.cache') . '/app');
        $cache->setNamespace('Maintained');

        return $cache;
    },

    'storage.repositories' => function (ContainerInterface $c) {
        $backend = new StorageWithTransformers(
            new FileStorage($c->get('directory.data') . '/repositories.json')
        );
        $backend->addTransformer(new JsonEncoder(true));
        $storage = new MapWithTransformers(
            new MapAdapter($backend)
        );
        $storage->addTransformer(new ObjectArrayMapper(Repository::class));
        return $storage;
    },
    'storage.statistics' => function (ContainerInterface $c) {
        $storage = new MapWithTransformers(
            new MultipleFileStorage($c->get('directory.data') . '/statistics')
        );
        $storage->addTransformer(new PhpSerializeEncoder);
        return $storage;
    },

    // GitHub API
    Client::class => function (ContainerInterface $c) {
        $cacheDirectory = $c->get('directory.cache') . '/github';

        $client = new Client(
            new CachedHttpClient(['cache_dir' => $cacheDirectory])
        );

        $authToken = $c->get('github.auth_token');
        if ($authToken) {
            $client->authenticate($authToken, null, Client::AUTH_HTTP_TOKEN);
        }

        return $client;
    },

];
