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
     * Date the issue has been opened.
     *
     * @var DateTime
     */
    private $openedAt;

    /**
     * Duration the issue has been opened for.
     *
     * @var TimeInterval
     */
    private $openedFor;

    /**
     * @var string[]
     */
    private $labels = [];

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

        $issue->openedAt = new DateTime($data['created_at']);
        if (! $issue->open) {
            $endDate = new DateTime($data['closed_at']);
        } else {
            $endDate = new DateTime();
        }

        $issue->openedFor = TimeInterval::from($endDate, $issue->openedAt);

        $labels = [];
        foreach ($data['labels'] as $dataLabel) {
            $labels[] = $dataLabel['name'];
        }
        $issue->labels = $labels;

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
     * @return DateTime
     */
    public function getOpenedAt()
    {
        return $this->openedAt;
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

    /**
     * @return string[]
     */
    public function getLabels()
    {
        return $this->labels;
    }
}
