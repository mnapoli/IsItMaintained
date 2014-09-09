<?php

namespace Maintained\Statistics;

use Maintained\Diagnostic;

/**
 * Computes statistics.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class StatisticsComputer implements StatisticsProvider
{
    public function getStatistics($user, $repository)
    {
        $diagnostic = new Diagnostic($user . '/' . $repository);

        $statistics = new Statistics();
        $statistics->resolutionTime = $diagnostic->computeMedian();

        return $statistics;
    }
}
