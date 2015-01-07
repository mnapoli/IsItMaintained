<?php

namespace Maintained\Statistics;

use BlackBox\MapStorage;

/**
 * Wraps and caches another provider.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CachedStatisticsProvider implements StatisticsProvider
{
    /**
     * @var MapStorage
     */
    private $cache;

    /**
     * @var StatisticsProvider
     */
    private $wrapped;

    public function __construct(MapStorage $cache, StatisticsProvider $wrapped)
    {
        $this->cache = $cache;
        $this->wrapped = $wrapped;
    }

    public function getStatistics($user, $repository)
    {
        $id = $user . '/' . $repository;

        $statistics = $this->cache->get($id);

        if (! $statistics instanceof Statistics) {
            $statistics = $this->wrapped->getStatistics($user, $repository);

            $this->cache->set($id, $statistics);
        }

        return $statistics;
    }
}
