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

    /**
     * @var string[]
     */
    private $excludedLabels;

    public function __construct(Client $github, array $excludedLabels)
    {
        $this->github = $github;
        $this->excludedLabels = $excludedLabels;
    }

    public function getStatistics($user, $repository)
    {
        $issues = $this->fetchIssues($user, $repository);
//        $collaborators = $this->fetchCollaborators($user, $repository);

//        $issues = $this->excludeIssuesCreatedByCollaborators($issues, $collaborators);
        $issues = $this->excludeIssuesByLabels($issues, $this->excludedLabels);

        $latestIssues = $this->keepLatestIssues($issues);

        $statistics = new Statistics();
        $statistics->resolutionTime = $this->computeResolutionTime($latestIssues);
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
     * @param Issue[]  $issues
     * @param string[] $labels
     * @return Issue[]
     */
    private function excludeIssuesByLabels(array $issues, array $labels)
    {
        $regex = '/^(' . implode(')|(', $labels) . ')$/i';

        return array_filter($issues, function (Issue $issue) use ($labels, $regex) {
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
     * @param Issue[]  $issues
     * @return Issue[]
     */
    private function keepLatestIssues(array $issues)
    {
        $sixMonthsAgo = new \DateTime('-6 month');

        return array_filter($issues, function (Issue $issue) use ($sixMonthsAgo) {
            if ($issue->isOpen()) {
                return true;
            }
            return $issue->getOpenedAt() > $sixMonthsAgo;
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

        return array_map(function ($user) {
            return $user['login'];
        }, $collaborators);
    }
}
