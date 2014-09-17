<?php

namespace Maintained\Application\Controller;

use Twig_Environment;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Error404Controller
{
    public function __invoke(Twig_Environment $twig)
    {
        header('HTTP/1.0 404 Not Found');
        echo $twig->render('404.twig');
    }
}
