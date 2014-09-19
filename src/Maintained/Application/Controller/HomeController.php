<?php

namespace Maintained\Application\Controller;

use Maintained\Storage\Storage;
use Twig_Environment;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class HomeController
{
    public function __invoke(Twig_Environment $twig, Storage $storage)
    {
        $latestRepositories = array_reverse($storage->retrieve('repositories'));
        $latestRepositories = array_slice($latestRepositories, 0, 9);

        $showcase = [
            'symfony/symfony'           => 'Symfony 2',
            'zendframework/zf2'         => 'Zend Framework 2',
            'zendframework/zf1'         => 'Zend Framework 1',
            'codeguy/Slim'              => 'Slim',
            'silexphp/Silex'            => 'Silex',
            'cakephp/cakephp'           => 'CakePHP',
            'sebastianbergmann/phpunit' => 'PHPUnit',
            'piwik/piwik'               => 'Piwik',
            'guzzle/guzzle'             => 'Guzzle',
        ];

        echo $twig->render('home.twig', [
            'latestRepositories' => $latestRepositories,
            'showcase' => $showcase,
        ]);
    }
}
