<?php

namespace Maintained;

use Github\Client;
use Github\HttpClient\CachedHttpClient;

/**
 * Performs the diagnostic of a repository.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Diagnostic
{
    /**
     * @var Client
     */
    private $github;

    /**
     * @var Issue[]
     */
    private $issues;

    /**
     * @var string[]
     */
    private $collaborators;

    public function __construct($repository)
    {
        // TODO dependency injection
        $this->github = new Client(
            new CachedHttpClient(['cache_dir' => __DIR__ . '/../../app/cache/github'])
        );

        list($user, $repository) = explode('/', $repository, 2);

        /** @var \GitHub\Api\Issue $issueApi */
        $issueApi = $this->github->api('issue');
        $issues = $issueApi->all($user, $repository, ['state' => 'all']);

        $this->issues = array_map(function (array $data) {
            return Issue::fromArray($data);
        }, $issues);

        /** @var \GitHub\Api\Repo $repositoryApi */
        $repositoryApi = $this->github->api('repo');
        $this->collaborators = $repositoryApi->collaborators()->all($user, $repository);
        $this->collaborators = array_map(function ($user) {
            return $user['login'];
        }, $this->collaborators);

        $this->excludeIssuesCreatedByCollaborators();
    }

    /**
     * @return TimeInterval
     */
    public function computeAverage()
    {
        $average = array_reduce($this->issues, function ($result, Issue $issue) {
            return $result + $issue->getOpenedFor()->toSeconds();
        }, 0);
        $average = $average / count($this->issues);

        return new TimeInterval($average);
    }

    /**
     * @return TimeInterval
     */
    public function computeMedian()
    {
        $durations = array_map(function (Issue $issue) {
            return $issue->getOpenedFor()->toSeconds();
        }, $this->issues);

        return new TimeInterval($this->median($durations));
    }

    private function median(array $array) {
        $count = count($array);

        if ($count == 0) {
            throw new \Exception('Median of an empty array is undefined');
        }

        sort($array, SORT_NUMERIC);

        $middleIndex = (int) floor($count / 2);

        // Handle the even case by averaging the middle 2 items
        if ($count % 2 == 0) {
            return ($array[$middleIndex] + $array[$middleIndex - 1]) / 2;
        }

        return $array[$middleIndex];
    }

    private function excludeIssuesCreatedByCollaborators()
    {
        $this->issues = array_filter($this->issues, function (Issue $issue) {
            return !in_array($issue->getAuthor(), $this->collaborators);
        });
    }
}
