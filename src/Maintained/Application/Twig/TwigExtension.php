<?php

namespace Maintained\Application\Twig;

use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig extension.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class TwigExtension extends Twig_Extension
{
    public function getName()
    {
        return 'maintained';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('badge', [$this, 'generateBadge'], ['is_safe' => ['html']]),
        ];
    }

    public function generateBadge($repository, $type = 'resolution')
    {
        return <<<HTML
<a href="/project/$repository"><img src="/badge/$type/$repository.svg"></a>
HTML;
    }
}
