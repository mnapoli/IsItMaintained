<?php

namespace Maintained\Badge;

use Doctrine\Common\Cache\Cache;

/**
 * Wraps and caches another provider.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CachedBadgeProvider implements BadgeProvider
{
    const CACHE_NAMESPACE = 'badge/';

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var BadgeProvider
     */
    private $wrapped;

    public function __construct(Cache $cache, BadgeProvider $wrapped)
    {
        $this->cache = $cache;
        $this->wrapped = $wrapped;
    }

    public function getResolutionBadge($user, $repository)
    {
        $key = self::CACHE_NAMESPACE . $user . '/' . $repository;

        $result = $this->cache->fetch($key);

        if ($result === false) {
            $result = $this->wrapped->getResolutionBadge($user, $repository);

            $this->cache->save($key, $result);
        }

        return $result;
    }
}
