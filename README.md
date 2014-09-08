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

## Demo

    $ cd web/
    $ php -S 0.0.0.0:8000 index.php

Visit an URL like [http://localhost:8000/badge/mnapoli/php-di.svg](http://localhost:8000/badge/mnapoli/php-di.svg) (badge).

Or run `bin/demo.php`.

## Requirements

- PHP 5.6
