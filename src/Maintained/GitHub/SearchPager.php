<?php

namespace Maintained\GitHub;

use Github\Api\ApiInterface;
use Github\Client;
use Github\HttpClient\Message\ResponseMediator;

/**
 * Pages search results.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class SearchPager
{
    /**
     * @var Client client
     */
    protected $client;

    /**
     * @var array pagination
     * Comes from pagination headers in Github API results
     */
    protected $pagination;

    /**
     * The Github client to use for pagination. This must be the same
     * instance that you got the Api instance from, i.e.:
     *
     * $client = new \Github\Client();
     * $api = $client->api('someApi');
     * $pager = new \Github\ResultPager($client);
     *
     * @param Client $client
     *
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchAll($q, $sort = 'updated', $order = 'desc')
    {
        $api = $this->client->search();

        // get the perPage from the api
        $perPage = $api->getPerPage();

        // Set parameters per_page to GitHub max to minimize number of requests
        $api->setPerPage(100);

        $api->issues($q, $sort, $order);
        $results = $api->issues($q, $sort, $order)['items'];
        $this->postFetch();

        while ($this->hasNext()) {
            $results = array_merge($results, $this->fetchNext()['items']);
        }

        // restore the perPage
        $api->setPerPage($perPage);

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function postFetch()
    {
        $this->pagination = ResponseMediator::getPagination($this->client->getHttpClient()->getLastResponse());
    }

    /**
     * {@inheritdoc}
     */
    public function hasNext()
    {
        return $this->has('next');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchNext()
    {
        return $this->get('next');
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrevious()
    {
        return $this->has('prev');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPrevious()
    {
        return $this->get('prev');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFirst()
    {
        return $this->get('first');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchLast()
    {
        return $this->get('last');
    }

    /**
     * {@inheritdoc}
     */
    protected function has($key)
    {
        return !empty($this->pagination) && isset($this->pagination[$key]);
    }

    /**
     * {@inheritdoc}
     */
    protected function get($key)
    {
        if ($this->has($key)) {
            $result = $this->client->getHttpClient()->get($this->pagination[$key]);
            $this->postFetch();

            return ResponseMediator::getContent($result);
        }
    }
}
