<?php

namespace Maintained;

use DateTime;

/**
 * Time interval.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class TimeInterval
{
    const TO_MINUTE = 60;
    const TO_HOUR = 3600;
    const TO_DAY = 86400;

    /**
     * @var int
     */
    private $seconds;

    public function __construct($seconds)
    {
        $this->seconds = (int) $seconds;
    }

    /**
     * @param DateTime $date1
     * @param DateTime $date2
     * @return TimeInterval
     */
    public static function from(DateTime $date1, DateTime $date2)
    {
        return new self($date1->getTimestamp() - $date2->getTimestamp());
    }

    /**
     * Format to a short display string.
     *
     * @return string
     */
    public function formatShort()
    {
        switch (true) {
            case $this->seconds < self::TO_MINUTE:
                return sprintf('%d s', $this->seconds);
            case $this->seconds < self::TO_HOUR:
                return sprintf('%d min', $this->toMinutes());
            case $this->seconds < self::TO_DAY:
                return sprintf('%d h', $this->toHours());
            default:
                return sprintf('%d d', $this->toDays());
        }
    }

    /**
     * Format to a long display string.
     *
     * @return string
     */
    public function formatLong()
    {
        switch (true) {
            case $this->seconds < self::TO_MINUTE:
                return sprintf('%d second(s)', $this->seconds);
            case $this->seconds < self::TO_HOUR:
                return sprintf('%d minute(s)', $this->toMinutes());
            case $this->seconds < self::TO_DAY:
                return sprintf('%d hour(s)', $this->toHours());
            default:
                return sprintf('%d day(s)', $this->toDays());
        }
    }

    /**
     * @return int
     */
    public function toSeconds()
    {
        return $this->seconds;
    }

    /**
     * @return int
     */
    public function toMinutes()
    {
        return $this->seconds / self::TO_MINUTE;
    }

    /**
     * @return int
     */
    public function toHours()
    {
        return $this->seconds / self::TO_HOUR;
    }

    /**
     * @return int
     */
    public function toDays()
    {
        return $this->seconds / self::TO_DAY;
    }

    public function __toString()
    {
        return $this->formatShort();
    }
}
