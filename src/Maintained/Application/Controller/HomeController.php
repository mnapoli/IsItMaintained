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
        $repositories = array_reverse($storage->retrieve('repositories'));

        echo $twig->render('home.twig', [
            'projects' => array_slice($repositories, 0, 9),
        ]);
    }
}
