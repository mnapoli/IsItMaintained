<?php

namespace Maintained\Statistics;

use Github\Client;
use Github\Exception\RuntimeException;
use Github\Exception\ValidationFailedException;
use Maintained\GitHub\SearchPager;
use Maintained\Issue;
use Maintained\TimeInterval;

/**
 * Computes statistics.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class StatisticsComputer implements StatisticsProvider
{
    /**
     * @var Client
     */
    private $github;

    /**
     * @var string[]
     */
    private $excludedLabels = [
        'enhancement',
        'feature',
        'task',
        'refactoring',
        'duplicate',
        'tests',
        'refactoring',
        'suggestion',
        'wip',
        'rfc',
        'wishlist',
        'question',
        'cleanup',
        'discussion',
        'meta',
    ];

    /**
     * @var string[]
     */
    private $excludedLabelRegexes = [
        '.*enhancement.*',
        '.*feature.*',
        '.*task.*',
        '.*refactoring.*',
        '.*duplicate.*',
        '.*test.*',
        '.*suggestion.*',
        '.*refactoring.*',
        '.*question.*',
        '.*cleanup.*',
        '.*discussion.*',
        '.*meta.*',
        '(.*[\s\.-])?wip',
        '(.*[\s\.-])?rfc',
        '(.*[\s\.-])?poc',
        '(.*[\s\.-])?dx',
    ];

    public function __construct(Client $github)
    {
        $this->github = $github;
    }

    public function getStatistics($user, $repository)
    {
        $issues = $this->fetchIssues($user, $repository);

        $issues = $this->filterIssuesByLabels($issues);

        $statistics = new Statistics();
        $statistics->resolutionTime = $this->computeResolutionTime($issues);
        $statistics->openIssuesRatio = $this->computeOpenIssueRatio($user, $repository);

        return $statistics;
    }

    /**
     * @param Issue[] $issues
     * @return TimeInterval
     */
    private function computeResolutionTime(array $issues)
    {
        $durations = array_map(function (Issue $issue) {
            return $issue->getOpenedFor()->toSeconds();
        }, $issues);

        return new TimeInterval($this->median($durations));
    }

    /**
     * @param string $user
     * @param string $repository
     * @return float
     */
    private function computeOpenIssueRatio($user, $repository)
    {
        $query = "repo:$user/$repository type:issue " . $this->getExcludedLabelsSearchString();

        $results = $this->github->search()->issues("$query state:open");
        $openCount = $results['total_count'];
        $results = $this->github->search()->issues("$query state:closed");
        $closedCount = $results['total_count'];

        $total = $openCount + $closedCount;

        return ($total !== 0) ? $openCount / $total : 0;
    }

    /**
     * @param Issue[]  $issues
     * @return Issue[]
     */
    private function filterIssuesByLabels(array $issues)
    {
        $regex = '/^(' . implode(')|(', $this->excludedLabelRegexes) . ')$/i';

        return array_filter($issues, function (Issue $issue) use ($regex) {
            foreach ($issue->getLabels() as $label) {
                $match = preg_match($regex, $label);

                if ($match === false) {
                    throw new \RuntimeException('Error while using the following regex: ' . $regex);
                } elseif ($match === 1) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * @param float[] $array
     * @return float
     */
    private function median(array $array)
    {
        $count = count($array);

        if ($count == 0) {
            return 0;
        }

        sort($array, SORT_NUMERIC);

        $middleIndex = (int) floor($count / 2);

        // Handle the even case by averaging the middle 2 items
        if ($count % 2 == 0) {
            return ($array[$middleIndex] + $array[$middleIndex - 1]) / 2;
        }

        return $array[$middleIndex];
    }

    public function fetchIssues($user, $repository)
    {
        $sixMonthsAgo = new \DateTime('-6 month');
        $sixMonthsAgo = $sixMonthsAgo->format('Y-m-d');

        // Pre-filter with labels to fetch as little issues as possible
        $excludedLabels = $this->getExcludedLabelsSearchString();

        $query = "repo:$user/$repository type:issue created:>$sixMonthsAgo $excludedLabels";

        $paginator = new SearchPager($this->github);
        try {
            $results = $paginator->fetchAll($query);
        } catch (ValidationFailedException $e) {
            if (strpos($e->getMessage(), 'Validation Failed: Field "q" is invalid') === 0) {
                throw new RuntimeException('Not Found');
            }
            throw $e;
        }

        return array_map(function (array $data) {
            return Issue::fromArray($data);
        }, $results);
    }

    /**
     * @return string
     */
    private function getExcludedLabelsSearchString()
    {
        $excludedLabels = array_map(function ($label) {
            return '-label:' . $label;
        }, $this->excludedLabels);

        return implode(' ', $excludedLabels);
    }
}
