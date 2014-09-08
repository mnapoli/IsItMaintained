<?php

namespace Maintained\Application\Controller;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class HomeController
{
    public function __invoke(\Twig_Environment $twig)
    {
        echo $twig->render('home.twig');
    }
}
