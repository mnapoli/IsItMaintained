<?php

namespace Maintained\Application\Command;

use Maintained\Statistics\StatisticsComputer;
use Maintained\Statistics\StatisticsProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command to show the statistics of a repository.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ShowStatisticsCommand extends Command
{
    /**
     * @var StatisticsProvider
     */
    private $statisticsProvider;

    public function __construct(StatisticsComputer $statisticsProvider)
    {
        $this->statisticsProvider = $statisticsProvider;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('stats:show')
            ->setDescription('Show the statistics of a repository')
            ->addArgument('repository', InputArgument::REQUIRED, 'In the form "user/repository"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $input->getArgument('repository');
        list($user, $repository) = explode('/', $repository, 2);

        $statistics = $this->statisticsProvider->getStatistics($user, $repository);

        $output->writeln(sprintf('<info>Median resolution time: %s</info>', $statistics->resolutionTime->formatLong()));
        $output->writeln(sprintf('<info>Open issues: %s%%</info>', intval($statistics->openIssuesRatio * 100)));
    }
}
