<?php

use Maintained\Application\Controller\BadgeController;
use Maintained\Application\Controller\HomeController;
use Maintained\Application\Controller\ProjectCheckController;
use Maintained\Application\Controller\ProjectController;

return [
    'home'    => [
        'pattern'    => '/',
        'controller' => HomeController::class,
    ],
    'check-project' => [
        'pattern'    => '/check/{user}/{repository}',
        'controller' => ProjectCheckController::class,
    ],
    'project' => [
        'pattern'    => '/project/{user}/{repository}',
        'controller' => ProjectController::class,
    ],
    'badge'   => [
        'pattern'    => '/badge/{badge}/{user}/{repository}.svg',
        'controller' => BadgeController::class,
    ],
];
