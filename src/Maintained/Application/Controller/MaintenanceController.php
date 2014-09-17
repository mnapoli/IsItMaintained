<?php

namespace Maintained\Application\Controller;

use Twig_Environment;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MaintenanceController
{
    public function __invoke(Twig_Environment $twig)
    {
        header('HTTP/1.0 503 Service Unavailable');
        echo $twig->render('maintenance.twig');
    }
}
