<?php

use Maintained\Application\Controller\BadgeController;
use Maintained\Application\Controller\HomeController;
use Maintained\Application\Controller\ProjectController;

return [
    'home'    => [
        'pattern'    => '/',
        'controller' => HomeController::class,
    ],
    'project' => [
        'pattern'    => '/project/{user}/{repository}',
        'controller' => ProjectController::class,
    ],
    'badge'   => [
        'pattern'    => '/badge/{user}/{repository}.svg',
        'controller' => BadgeController::class,
    ],
];
