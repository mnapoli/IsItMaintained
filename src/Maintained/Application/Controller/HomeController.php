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
