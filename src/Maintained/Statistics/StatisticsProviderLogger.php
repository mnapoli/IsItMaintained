<?php

namespace Maintained\Statistics;

use BlackBox\MapStorage;
use Maintained\Repository;

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
     * @var MapStorage
     */
    private $repositories;

    public function __construct(StatisticsProvider $wrapped, MapStorage $repositoryStorage)
    {
        $this->wrapped = $wrapped;
        $this->repositories = $repositoryStorage;
    }

    public function getStatistics($user, $repository)
    {
        // Fetch first, so that if an exception happens, we don't store the repository
        $statistics = $this->wrapped->getStatistics($user, $repository);

        // Store it
        $id = $user . '/' . $repository;
        if (! $this->repositories->get($id)) {
            $this->repositories->set($id, new Repository($id));
        }

        return $statistics;
    }
}
