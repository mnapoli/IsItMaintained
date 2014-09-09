# Maintained

Monitoring open source projects activity.

![](web/img/dude.png)

## Metrics

Done:

- Issue average & median closing time (collaborators issues excluded)

Ideas:

- Take into account pull requests? (are they already? need to check GitHub's API)
- Issue average/median acknowledgement time (i.e. first comment after the issue was open)
- Regularity of releases (i.e. average release delay)

## Run

    $ composer install
    $ bower install
    $ cd web/
    $ php -S 0.0.0.0:8000 index.php

The first time a badge is generated takes a few seconds, then it is cached. Be patient.

## Requirements

- PHP 5.6
- GD extension (`php5-gd`)
