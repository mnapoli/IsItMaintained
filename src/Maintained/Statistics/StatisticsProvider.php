<?php

namespace Maintained\Statistics;

/**
 * Provides statistics.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface StatisticsProvider
{
    /**
     * @param string $user
     * @param string $repository
     * @return Statistics
     */
    public function getStatistics($user, $repository);
}
