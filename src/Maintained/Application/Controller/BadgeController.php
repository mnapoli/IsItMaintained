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
    const COLOR_OK = '18bc9c';
    const COLOR_WARNING = 'CC9237';
    const COLOR_DANGER = '9C3838';

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

            $days = $statistics->resolutionTime->toDays();

            if ($days < 2) {
                $color = self::COLOR_OK;
            } elseif ($days < 8) {
                $color = self::COLOR_WARNING;
            } else {
                $color = self::COLOR_DANGER;
            }

            $badge = $this->poser->generate('resolution', $statistics->resolutionTime, $color, 'svg');
        } catch (ApiLimitExceedException $e) {
            $badge = $this->poser->generate('github-api', 'limit', self::COLOR_DANGER, 'svg');
        }

        header('Content-type: image/svg+xml');
        echo $badge;
    }
}
