<?php

namespace Maintained\Application\Controller;

use DI\Annotation\Inject;
use Github\Exception\RuntimeException;
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
        try {
            $statistics = $this->statisticsProvider->getStatistics($user, $repository);
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'Not Found') {
                echo $this->twig->render('not-found.twig');
                return;
            }
            echo $this->twig->render('github-limit.twig');
            return;
        }

        echo $this->twig->render('project.twig', [
            'repository'     => $user . '/' . $repository,
            'resolutionTime' => $statistics->resolutionTime,
            'openedIssues'   => round($statistics->openIssuesRatio * 100),
        ]);
    }
}
