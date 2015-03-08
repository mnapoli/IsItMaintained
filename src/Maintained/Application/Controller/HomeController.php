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
        $latestRepositories = iterator_to_array($this->repositories);
        $latestRepositories = array_reverse($latestRepositories);
        $latestRepositories = array_slice($latestRepositories, 0, 9);
        $latestRepositories = array_keys($latestRepositories);

        $showcase = [
            'rails/rails'            => 'Rails',
            'mitsuhiko/flask'        => 'Flask',
            'strongloop/express'     => 'Express',
            'symfony/symfony'        => 'Symfony',
            'zendframework/zf2'      => 'Zend Framework 2',
            'laravel/framework'      => 'Laravel',
            'angular/angular.js'     => 'AngularJS',
            'facebook/react'         => 'React',
            'robbyrussell/oh-my-zsh' => 'Oh My Zsh',
            'ariya/phantomjs'        => 'PhantomJS',
        ];

        echo $this->twig->render('home.twig', [
            'latestRepositories' => $latestRepositories,
            'showcase'           => $showcase,
        ]);
    }
}
