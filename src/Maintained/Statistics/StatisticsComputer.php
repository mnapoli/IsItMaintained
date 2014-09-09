<?php

namespace Maintained\Statistics;

use Github\Client;
use Github\ResultPager;
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

    public function __construct(Client $github)
    {
        $this->github = $github;
    }

    public function getStatistics($user, $repository)
    {
        $issues = $this->fetchIssues($user, $repository);
        $collaborators = $this->fetchCollaborators($user, $repository);

        $issues = $this->excludeIssuesCreatedByCollaborators($issues, $collaborators);

        $statistics = new Statistics();
        $statistics->resolutionTime = $this->computeResolutionTime($issues);
        $statistics->openIssuesRatio = $this->computeOpenIssueRatio($issues);

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
     * @param Issue[] $issues
     * @return float
     */
    private function computeOpenIssueRatio(array $issues)
    {
        if (empty($issues)) {
            return 0;
        }

        $openIssues = 0;

        foreach ($issues as $issue) {
            if ($issue->isOpen()) {
                $openIssues++;
            }
        }

        return $openIssues / count($issues);
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
     * @param float[] $array
     * @return float
     */
    private function median(array $array) {
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

    /**
     * @param string $user
     * @param string $repository
     * @return Issue[]
     */
    private function fetchIssues($user, $repository)
    {
        /** @var \GitHub\Api\Issue $issueApi */
        $issueApi = $this->github->api('issue');

        $paginator = new ResultPager($this->github);
        $issues = $paginator->fetchAll($issueApi, 'all', [
            $user,
            $repository,
            ['state' => 'all'],
        ]);

        $issues = array_map(function (array $data) {
            return Issue::fromArray($data);
        }, $issues);

        return $issues;
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

        $collaborators = array_map(function ($user) {
            return $user['login'];
        }, $collaborators);

        return $collaborators;
    }
}
