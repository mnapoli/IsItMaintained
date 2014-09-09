<?php

use DI\ContainerBuilder;

$builder = new ContainerBuilder();

$builder->addDefinitions(__DIR__ . '/config/config.php');

return $builder->build();
