<?php

namespace Maintained\Statistics;

use Maintained\TimeInterval;

/**
 * Statistics of an open source project.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Statistics
{
    /**
     * Average time for closing an issue.
     *
     * @var TimeInterval
     */
    public $resolutionTime;

    /**
     * Ratio of open issues.
     *
     * @var float
     */
    public $openIssuesRatio;
}
