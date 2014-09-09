<?php

namespace Maintained\Badge;

use Maintained\Diagnostic;
use PUGX\Poser\Poser;

/**
 * Provides a badge by generating it.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class BadgeGenerator implements BadgeProvider
{
    /**
     * @var Poser
     */
    private $poser;

    public function __construct(Poser $poser)
    {
        $this->poser = $poser;
    }

    public function getResolutionBadge($user, $repository)
    {
        $diagnostic = new Diagnostic($user . '/' . $repository);

        $median = $diagnostic->computeMedian()->formatShort();

        return $this->poser->generate('resolution', $median, '18bc9c', 'svg');
    }
}
