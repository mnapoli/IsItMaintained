<?php

use function DI\add;
use Maintained\Application\Command\ClearCacheCommand;
use Maintained\Application\Command\ShowStatisticsCommand;
use Maintained\Application\Command\UpdateStatisticsCommand;
use Maintained\Application\Command\WarmupCacheCommand;
use Maintained\Application\Middleware\MaintenanceMiddleware;
use Maintained\Statistics\CachedStatisticsProvider;
use Maintained\Statistics\StatisticsComputer;
use Maintained\Statistics\StatisticsProvider;
use Maintained\Statistics\StatisticsProviderLogger;
use function DI\factory;
use function DI\get;
use function DI\object;

$config = [

    'baseUrl' => 'http://isitmaintained.com',
    'maintenance' => false,

    'directory.cache' => __DIR__ . '/../../var/cache',
    'directory.data' => __DIR__ . '/../../var/data',
    'directory.logs' => __DIR__ . '/../../var/logs',

    // Piwik tracking
    'piwik.enabled' => false,
    'piwik.host' => null,
    'piwik.site_id' => null,

    // GitHub API
    'github.auth_token' => null,

    StatisticsProvider::class => get(CachedStatisticsProvider::class),
    CachedStatisticsProvider::class => object()
        ->constructorParameter('cache', get('storage.statistics'))
        ->constructorParameter('wrapped', get(StatisticsProviderLogger::class)),
    StatisticsProviderLogger::class => object()
        ->constructorParameter('wrapped', get(StatisticsComputer::class))
        ->constructorParameter('repositoryStorage', get('storage.repositories')),

    // CLI commands
    ClearCacheCommand::class => object()
        ->constructorParameter('cacheDirectory', get('directory.cache'))
        ->constructorParameter('dataDirectory', get('directory.data')),
    ShowStatisticsCommand::class => object(),
    WarmupCacheCommand::class => object()
        ->constructorParameter('repositoryStorage', get('storage.repositories')),
    UpdateStatisticsCommand::class => object()
        ->constructorParameter('repositoryStorage', get('storage.repositories'))
        ->constructorParameter('statisticsCache', get('storage.statistics')),

    // Middlewares
    MaintenanceMiddleware::class => object()
        ->constructorParameter('enabled', get('maintenance')),

];

return array_merge(
    $config,
    require __DIR__ . '/config.libraries.php',
    require __DIR__ . '/parameters.php'
);
