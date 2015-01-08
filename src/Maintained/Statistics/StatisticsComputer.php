<?php

namespace Maintained\Statistics;

use Github\Client;
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

        // Currently disabled because GitHub changed permissions: requires push access
//        $collaborators = $this->fetchCollaborators($user, $repository);
//        $issues = $this->excludeIssuesCreatedByCollaborators($issues, $collaborators);

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
        $results = $this->github->search()->issues("type:issue repo:$user/$repository state:open");
        $openCount = $results['total_count'];
        $results = $this->github->search()->issues("type:issue repo:$user/$repository state:closed");
        $closedCount = $results['total_count'];

        $total = $openCount + $closedCount;

        return ($total !== 0) ? $openCount / $total : 0;
    }

    /**
     * @param Issue[]  $issues
     * @param string[] $collaborators
     * @return Issue[]
     */
    private function excludeIssuesCreatedByCollaborators(array $issues, array $collaborators)
    {
        return array_filter($issues, function (Issue $issue) use ($collaborators) {
            return !in_array($issue->getAuthor(), $collaborators);
        });
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
        $excludedLabels = array_map(function ($label) {
            return '-label:' . $label;
        }, $this->excludedLabels);
        $excludedLabels = implode(' ', $excludedLabels);

        $query = "repo:$user/$repository type:issue created:>$sixMonthsAgo $excludedLabels";

        $paginator = new SearchPager($this->github);
        $results = $paginator->fetchAll($query);

        return array_map(function (array $data) {
            return Issue::fromArray($data);
        }, $results);
    }

    /**
     * @param string $user
     * @param string $repository
     * @return string[]
     */
    private function fetchCollaborators($user, $repository)
    {
        /** @var \GitHub\Api\Repo $repositoryApi */
        $repositoryApi = $this->github->api('repo');
        $collaborators = $repositoryApi->collaborators()->all($user, $repository);

        return array_map(function ($user) {
            return $user['login'];
        }, $collaborators);
    }
}
