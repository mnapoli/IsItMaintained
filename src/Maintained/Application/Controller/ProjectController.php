<?php

namespace Maintained\Application\Controller;

use Maintained\Statistics\StatisticsProvider;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ProjectController
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
        $statistics = $this->statisticsProvider->getStatistics($user, $repository);

        echo $this->twig->render('project.twig', [
            'repository'     => $user . '/' . $repository,
            'resolutionTime' => $statistics->resolutionTime,
        ]);
    }
}
