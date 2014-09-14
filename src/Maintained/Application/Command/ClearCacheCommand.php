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

    public function __construct($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cache:clear')
            ->setDescription('Clears the caches')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Which cache to clear (app, github, all). By default: "all"',
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

        if ($cache === 'app' || $cache === 'all') {
            $fs->remove($this->cacheDirectory . '/app');
            $output->writeln('<info>Application cache cleared</info>');
        }
    }
}
