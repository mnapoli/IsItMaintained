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
            'symfony/symfony'               => 'Symfony',
            'Ocramius/ProxyManager'         => 'ProxyManager',
            'Atlantic18/DoctrineExtensions' => 'DoctrineExtensions',
            'schmittjoh/serializer'         => 'JMS Serializer',
            'PHPOffice/PHPExcel'            => 'PHPExcel',
            'composer/composer'             => 'Composer',
            'Behat/Behat'                   => 'Behat',
            'kriswallsmith/assetic'         => 'Assetic',
            'sebastianbergmann/phpunit'     => 'PHPUnit',
        ];

        echo $twig->render('home.twig', [
            'projects' => $demoProjects,
        ]);
    }
}
