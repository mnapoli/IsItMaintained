<?php

namespace Maintained\Badge;

/**
 * Provides badges for repositories.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface BadgeProvider
{
    /**
     * @param string $user
     * @param string $repository
     * @return string
     */
    public function getResolutionBadge($user, $repository);
}
