<?php

use Maintained\Application\Command\ClearCacheCommand;
use Maintained\Application\Command\ShowStatisticsCommand;
use Maintained\Statistics\CachedStatisticsProvider;
use Maintained\Statistics\StatisticsComputer;
use Maintained\Statistics\StatisticsProvider;
use Maintained\Statistics\StatisticsProviderLogger;
use Maintained\Storage\JsonFileStorage;
use Maintained\Storage\Storage;
use function DI\factory;
use function DI\link;
use function DI\object;

return [
    'baseUrl' => 'http://isitmaintained.com',

    'directory.cache' => __DIR__ . '/../../app/cache',
    'directory.data' => __DIR__ . '/../../app/data',

    // Routing
    'routes' => require __DIR__ . '/routes.php',

    // Piwik tracking
    'piwik.enabled' => false,
    'piwik.host' => null,
    'piwik.site_id' => null,

    // GitHub API
    'github.auth_token' => null,

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
