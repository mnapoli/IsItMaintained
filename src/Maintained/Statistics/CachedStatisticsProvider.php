<?php

namespace Maintained\Statistics;

use Doctrine\Common\Cache\Cache;

/**
 * Wraps and caches another provider.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CachedStatisticsProvider implements StatisticsProvider
{
    const CACHE_NAMESPACE = 'statistics/';

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var StatisticsProvider
     */
    private $wrapped;

    public function __construct(Cache $cache, StatisticsProvider $wrapped)
    {
        $this->cache = $cache;
        $this->wrapped = $wrapped;
    }

    public function getStatistics($user, $repository)
    {
        $key = self::CACHE_NAMESPACE . $user . '/' . $repository;

        $statistics = $this->cache->fetch($key);

        if ($statistics === false) {
            $statistics = $this->wrapped->getStatistics($user, $repository);

            $this->cache->save($key, $statistics);
        }

        return $statistics;
    }
}
