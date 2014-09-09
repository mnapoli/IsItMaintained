<?php

namespace Maintained\Application\Command;

use Symfony\Component\Console\Command\Command;
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
            ->setDescription('Clears the caches');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        $fs->remove($this->cacheDirectory . '/app');
        $fs->remove($this->cacheDirectory . '/github');
    }
}
