<?php

namespace Maintained\Application\Controller;

use Maintained\Diagnostic;
use PUGX\Poser\Poser;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class BadgeController
{
    public function __invoke($user, $repository, Poser $badgeGenerator)
    {
        $diagnostic = new Diagnostic($user . '/' . $repository);

        $median = $diagnostic->computeMedian()->formatShort();

        echo $badgeGenerator->generate('resolution', $median, '428F7E', 'svg');
    }
}
