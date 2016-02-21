<?php

namespace Maintained\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MaintenanceMiddleware
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(Twig_Environment $twig, bool $enabled)
    {
        $this->twig = $twig;
        $this->enabled = $enabled;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface
    {
        if (!$this->enabled) {
            return $next($request, $response);
        }

        $response = $response->withStatus(503);
        $response->getBody()->write($this->twig->render('/app/views/maintenance.twig'));

        return $response;
    }
}
