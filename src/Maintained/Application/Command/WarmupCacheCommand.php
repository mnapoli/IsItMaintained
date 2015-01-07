<?php

namespace Maintained\Application\Command;

use BlackBox\MapStorage;
use Maintained\Repository;
use Maintained\Statistics\StatisticsProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command to warmup the caches.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class WarmupCacheCommand extends Command
{
    /**
     * @var MapStorage
     */
    private $repositories;

    /**
     * @var StatisticsProvider
     */
    private $statisticsProvider;

    public function __construct(MapStorage $repositoryStorage, StatisticsProvider $statisticsProvider)
    {
        $this->repositories = $repositoryStorage;
        $this->statisticsProvider = $statisticsProvider;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cache:warmup')
            ->setDescription('Warmup the caches');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->repositories as $id => $repository) {
            /** @var Repository $repository */
            $repository = $this->repositories->get($id);
            $slug = $repository->getName();

            $output->writeln(sprintf('Caching statistics for <info>%s</info>', $slug));

            list($user, $repo) = explode('/', $slug, 2);

            $this->statisticsProvider->getStatistics($user, $repo);
        }
    }
}
