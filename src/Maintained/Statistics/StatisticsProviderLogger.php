<?php

namespace Maintained\Statistics;

use Maintained\Storage\Storage;

/**
 * Logs accesses to the statistics provider.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class StatisticsProviderLogger implements StatisticsProvider
{
    /**
     * @var StatisticsProvider
     */
    private $wrapped;

    /**
     * @var Storage
     */
    private $storage;

    public function __construct(StatisticsProvider $wrapped, Storage $storage)
    {
        $this->wrapped = $wrapped;
        $this->storage = $storage;
    }

    public function getStatistics($user, $repository)
    {
        $id = $user . '/' . $repository;

        $repositories = $this->storage->retrieve('repositories');
        $repositories = is_array($repositories) ? $repositories : [];

        // Fetch first, so that if an exception happens, we don't store the repository
        $statistics = $this->wrapped->getStatistics($user, $repository);

        if (! in_array($id, $repositories)) {
            $repositories[] = $id;
            $this->storage->store('repositories', $repositories);
        }

        return $statistics;
    }
}
