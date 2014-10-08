<?php

namespace Maintained\Application\Controller;

use BlackBox\MapStorage;
use DI\Annotation\Inject;
use Twig_Environment;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class HomeController
{
    /**
     * @Inject
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @Inject("storage.repositories")
     * @var MapStorage
     */
    private $repositories;

    public function __invoke()
    {
        $latestRepositories = $this->repositories->getData() ?: [];
        $latestRepositories = array_reverse($latestRepositories);
        $latestRepositories = array_slice($latestRepositories, 0, 9);
        $latestRepositories = array_keys($latestRepositories);

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

        echo $this->twig->render('home.twig', [
            'latestRepositories' => $latestRepositories,
            'showcase' => $showcase,
        ]);
    }
}
