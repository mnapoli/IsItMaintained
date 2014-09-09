<?php

namespace Maintained\Application\Controller;

use DI\Annotation\Inject;
use Github\Exception\ApiLimitExceedException;
use Maintained\Statistics\StatisticsProvider;
use PUGX\Poser\Poser;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class BadgeController
{
    /**
     * @Inject
     * @var StatisticsProvider
     */
    private $statisticsProvider;

    /**
     * @Inject
     * @var Poser
     */
    private $poser;

    public function __invoke($user, $repository)
    {
        try {
            $statistics = $this->statisticsProvider->getStatistics($user, $repository);

            $badge = $this->poser->generate('resolution', $statistics->resolutionTime, '18bc9c', 'svg');
        } catch (ApiLimitExceedException $e) {
            $badge = $this->poser->generate('github-api', 'limit', '9C3838', 'svg');
        }

        header('Content-type: image/svg+xml');
        echo $badge;
    }
}
