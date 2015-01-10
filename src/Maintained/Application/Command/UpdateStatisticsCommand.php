<?php

namespace Maintained\Application\Command;

use BlackBox\MapStorage;
use Maintained\Repository;
use Maintained\Statistics\StatisticsProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * CLI command to update the cached statistics of a repository.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class UpdateStatisticsCommand extends Command
{
    /**
     * @var MapStorage
     */
    private $repositoryStorage;

    /**
     * @var MapStorage
     */
    private $statisticsCache;

    /**
     * @var StatisticsProvider
     */
    private $statisticsProvider;

    public function __construct(
        MapStorage $repositoryStorage,
        MapStorage $statisticsCache,
        StatisticsProvider $statisticsProvider
    ) {
        $this->repositoryStorage = $repositoryStorage;
        $this->statisticsCache = $statisticsCache;
        $this->statisticsProvider = $statisticsProvider;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('stats:update')
            ->setDescription('Updates the cached statistics')
            ->addArgument('repository', InputArgument::OPTIONAL, 'In the form "user/repository"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler($this->getName());
        if (! $lock->lock()) {
            $output->writeln('The command is already running in another process.');
            return 0;
        }

        $repositoryName = $input->getArgument('repository');
        if ($repositoryName) {
            $repositories = [ $this->repositoryStorage->get($repositoryName) ];
        } else {
            $repositories = $this->getRepositoriesToUpdate();
        }

        foreach ($repositories as $repository) {
            $output->writeln(sprintf('Updating <info>%s</info>', $repository->getName()));

            $timer = microtime(true);

            $this->update($repository, $output);

            $output->writeln(sprintf('Took %ds', microtime(true) - $timer));
        }

        $lock->release();
        return 0;
    }

    /**
     * @return Repository[]
     */
    private function getRepositoriesToUpdate()
    {
        $repositories = iterator_to_array($this->repositoryStorage);

        usort($repositories, function (Repository $a, Repository $b) {
            return $a->getLastUpdateTimestamp() - $b->getLastUpdateTimestamp();
        });

        // For now processes just one
        return [ reset($repositories) ];
    }

    private function update(Repository $repository, OutputInterface $output)
    {
        // Clear the cache
        $this->statisticsCache->set($repository->getName(), null);

        // Warmup the cache
        try {
            list($user, $repositoryName) = explode('/', $repository->getName(), 2);
            $this->statisticsProvider->getStatistics($user, $repositoryName);
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                '<error>Error while fetching statistics for %s</error>',
                $repository->getName()
            ));
            $output->writeln(sprintf('<error>%s: %s</error>', get_class($e), $e->getMessage()));
        }

        // Mark the repository updated
        $repository->update();
        $this->repositoryStorage->set($repository->getName(), $repository);
    }
}
