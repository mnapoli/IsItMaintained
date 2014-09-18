<?php

namespace Maintained\Application\Controller;

use DI\Annotation\Inject;
use Github\Exception\RuntimeException;
use Maintained\Statistics\Statistics;
use Maintained\Statistics\StatisticsProvider;
use PUGX\Poser\Image;
use PUGX\Poser\Poser;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class BadgeController
{
    const BADGE_RESOLUTION = 'resolution';
    const BADGE_OPEN_ISSUES = 'open';

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

    public function __invoke($badge, $user, $repository)
    {
        try {
            $statistics = $this->statisticsProvider->getStatistics($user, $repository);

            switch ($badge) {
                case self::BADGE_OPEN_ISSUES:
                    $badge = $this->createOpenIssuesBadge($statistics);
                    break;
                case self::BADGE_RESOLUTION:
                default:
                    $badge = $this->createResolutionBadge($statistics);
                    break;
            }
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'Not Found') {
                $badge = $this->poser->generate('github', 'not-found', self::COLOR_DANGER, 'svg');
            } else {
                $badge = $this->poser->generate('github-api', 'limit', self::COLOR_DANGER, 'svg');
            }
        }

        // Cache the badge for 1 day
        header('Cache-Control: max-age=86400');
        header('Content-type: image/svg+xml');

        echo $badge;
    }

    /**
     * @param Statistics $statistics
     * @return Image
     */
    private function createResolutionBadge(Statistics $statistics)
    {
        $days = $statistics->resolutionTime->toDays();

        if ($days < 2) {
            $color = self::COLOR_OK;
        } elseif ($days < 8) {
            $color = self::COLOR_WARNING;
        } else {
            $color = self::COLOR_DANGER;
        }

        return $this->poser->generate('issue resolution', $statistics->resolutionTime->formatShort(), $color, 'svg');
    }

    /**
     * @param Statistics $statistics
     * @return Image
     */
    private function createOpenIssuesBadge(Statistics $statistics)
    {
        $ratio = $statistics->openIssuesRatio;

        if ($ratio < 0.1) {
            $color = self::COLOR_OK;
        } elseif ($ratio < 0.2) {
            $color = self::COLOR_WARNING;
        } else {
            $color = self::COLOR_DANGER;
        }

        return $this->poser->generate('open issues', round($ratio * 100) . '%', $color, 'svg');
    }
}
