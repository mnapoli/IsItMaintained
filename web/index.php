<?php

use Maintained\Application\Controller\BadgeController;
use Maintained\Application\Controller\HomeController;
use Maintained\Application\Controller\ProjectCheckController;
use Maintained\Application\Controller\ProjectController;
use Maintained\Application\Middleware\Error404Middleware;
use Maintained\Application\Middleware\MaintenanceMiddleware;
use Monolog\ErrorHandler;
use Psr\Log\LoggerInterface;
use Stratify\ErrorHandlerModule\ErrorHandlerMiddleware;
use Stratify\Framework\Application;
use function Stratify\Framework\pipe;
use function Stratify\Framework\router;
use function Stratify\Router\route;

if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']))) {
    return false;
}

require_once __DIR__ . '/../.puli/GeneratedPuliFactory.php';
require __DIR__ . '/../vendor/autoload.php';

$http = pipe([
    ErrorHandlerMiddleware::class,
    MaintenanceMiddleware::class,

    router([
        '/' => route(HomeController::class, 'home'),
        '/check/{user}/{repository}' => route(ProjectCheckController::class, 'check-project'),
        '/project/{user}/{repository}' => route(ProjectController::class, 'project'),
        '/badge/{badge}/{user}/{repository}.svg' => route(BadgeController::class, 'badge'),
    ]),

    // If no route matched
    Error404Middleware::class,
]);

$app = new Application;
ErrorHandler::register($app->getContainer()->get(LoggerInterface::class));
$app->http($http)->run();
