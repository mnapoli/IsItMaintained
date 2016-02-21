<?php

namespace Maintained\Application\Controller;

use DI\Annotation\Inject;
use Github\Exception\RuntimeException;
use Maintained\Statistics\StatisticsProvider;

/**
 * This is the HTML part that is returned when a user "Check a project" from the home page.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ProjectCheckController
{
    /**
     * @Inject
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @Inject
     * @var StatisticsProvider
     */
    private $statisticsProvider;

    public function __invoke($user, $repository)
    {
        try {
            // Fetch statistics eagerly so that results are cached
            // Doing this now avoids that the many badges trigger this several times in parallel
            $this->statisticsProvider->getStatistics($user, $repository);
        } catch (RuntimeException $e) {
            // Silence exception: the error will show on the badges
        }

        return $this->twig->render('project-check.twig', [
            'repository' => $user . '/' . $repository,
        ]);
    }
}
