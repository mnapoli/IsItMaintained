<?php

require_once __DIR__ . '/../vendor/autoload.php';

$repositories = [
    'mnapoli/PHP-DI',
    'Atlantic18/DoctrineExtensions',
    'Ocramius/ProxyManager',
];

foreach ($repositories as $repository) {
    $diagnostic = new \Maintained\Diagnostic($repository);

    echo $repository . PHP_EOL;

    echo $diagnostic->computeAverage() . PHP_EOL;
    echo $diagnostic->computeMedian() . PHP_EOL;

    echo PHP_EOL;
}
