<?php

namespace Maintained\Application\Controller;

use Github\Exception\ApiLimitExceedException;
use Maintained\Badge\BadgeProvider;
use PUGX\Poser\Poser;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class BadgeController
{
    /**
     * @Inject
     * @var BadgeProvider
     */
    private $badgeProvider;

    /**
     * @Inject
     * @var Poser
     */
    private $poser;

    public function __invoke($user, $repository)
    {
        try {
            $badge = $this->badgeProvider->getResolutionBadge($user, $repository);
        } catch (ApiLimitExceedException $e) {
            $badge = $this->poser->generate('github-api', 'limit', '9C3838', 'svg');
        }

        header('Content-type: image/svg+xml');
        echo $badge;
    }
}
