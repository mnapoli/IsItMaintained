<?php

use Maintained\Application\Command\ClearCacheCommand;
use Maintained\Application\Command\ShowStatisticsCommand;
use Maintained\Application\Command\UpdateStatisticsCommand;
use Maintained\Application\Command\WarmupCacheCommand;
use Maintained\Statistics\CachedStatisticsProvider;
use Maintained\Statistics\StatisticsComputer;
use Maintained\Statistics\StatisticsProvider;
use Maintained\Statistics\StatisticsProviderLogger;
use function DI\factory;
use function DI\link;
use function DI\object;

return [
    'baseUrl' => 'http://isitmaintained.com',
    'maintenance' => false,

    'directory.cache' => __DIR__ . '/../../app/cache',
    'directory.data' => __DIR__ . '/../../app/data',
    'directory.logs' => __DIR__ . '/../../app/logs',

    // Routing
    'routes' => require __DIR__ . '/routes.php',

    // Piwik tracking
    'piwik.enabled' => false,
    'piwik.host' => null,
    'piwik.site_id' => null,

    // GitHub API
    'github.auth_token' => null,

    StatisticsProvider::class => link(CachedStatisticsProvider::class),
    CachedStatisticsProvider::class => object()
        ->constructorParameter('cache', link('storage.statistics'))
        ->constructorParameter('wrapped', link(StatisticsProviderLogger::class)),
    StatisticsProviderLogger::class => object()
        ->constructorParameter('wrapped', link(StatisticsComputer::class))
        ->constructorParameter('repositoryStorage', link('storage.repositories')),

    // CLI commands
    ClearCacheCommand::class => object()
        ->constructorParameter('cacheDirectory', link('directory.cache'))
        ->constructorParameter('dataDirectory', link('directory.data')),
    ShowStatisticsCommand::class => object(),
    WarmupCacheCommand::class => object()
        ->constructorParameter('repositoryStorage', link('storage.repositories')),
    UpdateStatisticsCommand::class => object()
        ->constructorParameter('repositoryStorage', link('storage.repositories'))
        ->constructorParameter('statisticsCache', link('storage.statistics')),
];
