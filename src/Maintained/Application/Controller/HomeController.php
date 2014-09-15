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
            'symfony/symfony'               => 'Symfony 2',
            'zendframework/zf2'             => 'Zend Framework 2',
            'zendframework/zf1'             => 'Zend Framework 1',
            'sebastianbergmann/phpunit'     => 'PHPUnit',
            'Behat/Behat'                   => 'Behat',
            'Atlantic18/DoctrineExtensions' => 'DoctrineExtensions',
            'schmittjoh/serializer'         => 'JMS Serializer',
            'Ocramius/ProxyManager'         => 'ProxyManager',
            'kriswallsmith/assetic'         => 'Assetic',
        ];

        echo $twig->render('home.twig', [
            'projects' => $demoProjects,
        ]);
    }
}
