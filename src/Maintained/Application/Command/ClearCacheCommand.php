<?php

namespace Maintained\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * CLI command to clear the caches.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ClearCacheCommand extends Command
{
    private $cacheDirectory;
    private $dataDirectory;

    public function __construct($cacheDirectory, $dataDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->dataDirectory = $dataDirectory;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cache:clear')
            ->setDescription('Clears the caches')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Which cache to clear (statistics, github, all). By default: "all"',
                'all'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        $cache = $input->getArgument('name');

        if ($cache === 'github' || $cache === 'all') {
            $fs->remove($this->cacheDirectory . '/github');
            $output->writeln('<info>GitHub cache cleared</info>');
        }

        if ($cache === 'statistics' || $cache === 'all') {
            $fs->remove($this->dataDirectory . '/statistics');
            $output->writeln('<info>Statistics cache cleared</info>');
        }
    }
}
