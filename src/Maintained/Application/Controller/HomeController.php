<?php

namespace Maintained\Application\Controller;

use Twig_Environment;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class HomeController
{
    public function __invoke(Twig_Environment $twig)
    {
        $demoProjects = [
            'symfony/symfony'           => 'Symfony 2',
            'zendframework/zf2'         => 'Zend Framework 2',
            'zendframework/zf1'         => 'Zend Framework 1',
            'codeguy/Slim'              => 'Slim',
            'silexphp/Silex'            => 'Silex',
            'sebastianbergmann/phpunit' => 'PHPUnit',
            'auraphp/Aura.Sql'          => 'Aura.Sql',
            'Behat/Behat'               => 'Behat',
            'schmittjoh/serializer'     => 'Serializer',
            'kriswallsmith/assetic'     => 'Assetic',
            'thephpleague/flysystem'    => 'Flysystem',
            'guzzle/guzzle'             => 'Guzzle',
        ];

        echo $twig->render('home.twig', [
            'projects' => $demoProjects,
        ]);
    }
}
