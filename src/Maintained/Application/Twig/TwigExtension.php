<?php

namespace Maintained\Application\Twig;

use Interop\Container\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig extension.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class TwigExtension extends Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'maintained';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('projectUrl', [$this, 'projectUrl']),
            new Twig_SimpleFunction('badgeUrl', [$this, 'badgeUrl']),
            new Twig_SimpleFunction('badgeHtml', [$this, 'badgeHtml'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('badgeMarkdown', [$this, 'badgeMarkdown']),
            new Twig_SimpleFunction('baseUrl', [$this, 'getBaseUrl']),
        ];
    }

    public function projectUrl($repository, $withBaseUrl = false)
    {
        $baseUrl = $withBaseUrl ? $this->getBaseUrl() : '';

        return "$baseUrl/project/$repository";
    }

    public function badgeUrl($repository, $type = 'resolution', $withBaseUrl = false)
    {
        $baseUrl = $withBaseUrl ? $this->getBaseUrl() : '';

        return "$baseUrl/badge/$type/$repository.svg";
    }

    public function badgeHtml($repository, $type = 'resolution')
    {
        $url = $this->badgeUrl($repository, $type);
        $projectUrl = $this->projectUrl($repository);

        return <<<HTML
<a href="$projectUrl"><img src="$url"></a>
HTML;
    }

    public function badgeMarkdown($repository, $type = 'resolution')
    {
        $badgeUrl = $this->badgeUrl($repository, $type, true);
        $projectUrl = $this->projectUrl($repository, true);
        $description = $this->getBadgeDescription($type);

        return "[![$description]($badgeUrl)]($projectUrl)";
    }

    public function getBaseUrl()
    {
        return $this->container->get('baseUrl');
    }

    private function getBadgeDescription($type)
    {
        switch ($type) {
            case 'open':
                return 'Percentage of issues still open';
            case 'resolution':
            default:
                return 'Average time to resolve an issue';
        }
    }
}
