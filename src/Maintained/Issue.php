<?php

namespace Maintained;

use DateTime;

/**
 * Issue
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Issue
{
    const STATE_OPEN = 'open';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $author;

    /**
     * @var bool
     */
    private $open;

    /**
     * Duration the issue has been opened for.
     *
     * @var TimeInterval
     */
    private $openedFor;

    private function __construct($id)
    {
        $this->id = (int) $id;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data)
    {
        $issue = new self($data['number']);

        $issue->author = $data['user']['login'];
        $issue->open = ($data['state'] === self::STATE_OPEN);

        $openingDate = new DateTime($data['created_at']);
        if (! $issue->open) {
            $endDate = new DateTime($data['closed_at']);
        } else {
            $endDate = new DateTime();
        }

        $issue->openedFor = TimeInterval::from($endDate, $openingDate);

        return $issue;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return TimeInterval
     */
    public function getOpenedFor()
    {
        return $this->openedFor;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return $this->open;
    }
}
