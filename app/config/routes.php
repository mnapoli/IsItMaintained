<?php

use Maintained\Application\Controller\BadgeController;
use Maintained\Application\Controller\HomeController;

return [
    'home' => [
        'pattern'    => '/',
        'controller' => HomeController::class,
    ],
    'badge' => [
        'pattern'    => '/badge/{user}/{repository}.svg',
        'controller' => BadgeController::class,
    ],
];
