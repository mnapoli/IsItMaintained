<?php

namespace Maintained;

use DateTime;

/**
 * Repository
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Repository
{
    /**
     * @var string
     */
    private $name;

    /**
     * Timestamp
     *
     * @var int|null
     */
    private $lastUpdate;

    public function __construct($name)
    {
        $this->name = (string) $name;
        $this->update();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function update()
    {
        $this->lastUpdate = time();
    }

    /**
     * @return DateTime|null
     */
    public function getLastUpdate()
    {
        if ($this->lastUpdate === null) {
            return null;
        }

        return DateTime::createFromFormat('U', $this->lastUpdate);
    }

    /**
     * @return int|null
     */
    public function getLastUpdateTimestamp()
    {
        if ($this->lastUpdate === null) {
            return null;
        }

        return $this->lastUpdate;
    }
}
