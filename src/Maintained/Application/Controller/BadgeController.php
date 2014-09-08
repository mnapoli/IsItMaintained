<?php

namespace Maintained\Application\Controller;

use Maintained\Badge\BadgeProvider;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class BadgeController
{
    public function __invoke($user, $repository, BadgeProvider $badgeProvider)
    {
        header('Content-type: image/svg+xml');

        echo $badgeProvider->getResolutionBadge($user, $repository);
    }
}
