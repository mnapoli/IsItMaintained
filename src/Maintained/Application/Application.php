<?php
declare(strict_types = 1);

namespace Maintained\Application;

use DI\ContainerBuilder;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Application extends \Stratify\Framework\Application
{
    public function __construct()
    {
        $modules = [
            'error-handler',
            'twig',
            'app',
        ];

        parent::__construct($modules);
    }

    protected function configureContainerBuilder(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->useAnnotations(true);
    }
}
