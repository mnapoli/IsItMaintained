<?php

namespace Maintained\Application\Command;

use Maintained\Statistics\StatisticsProvider;
use Maintained\Storage\Storage;
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
     * @var Storage
     */
    private $storage;

    /**
     * @var StatisticsProvider
     */
    private $statisticsProvider;

    public function __construct(Storage $storage, StatisticsProvider $statisticsProvider)
    {
        $this->storage = $storage;
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
        $repositories = $this->storage->retrieve('repositories');
        $repositories = is_array($repositories) ? $repositories : [];

        foreach ($repositories as $repository) {
            $output->writeln(sprintf('Caching statistics for <info>%s</info>', $repository));

            list($user, $repository) = explode('/', $repository, 2);

            $this->statisticsProvider->getStatistics($user, $repository);
        }
    }
}
