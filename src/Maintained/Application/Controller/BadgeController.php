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
        echo $badgeProvider->getResolutionBadge($user, $repository);
    }
}
