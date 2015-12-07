<?php

namespace NewRelic;

use GuzzleHttp\Client;
use NewRelic\Entity\Insights\EventCollection;
use Respect\Validation\Validator;

class Insights
{
    private $key;

    public function __construct(Client $client, $key)
    {
        $this->client = $client;
        $this->key = $key;
        $baseUrl = $this->client->getConfig('base_uri');
        Validator::notEmpty()
            ->url()
            ->setName("URL for NewRelic's Insights API")
            ->assert($baseUrl);
    }

    public function sendEvent(EventCollection $events)
    {
        $promise = $this->client->postAsync('/events', [
            'body' => json_encode($events),
            'headers' => [
                'X-Insert-Key' => $this->key,
                'Content-Type' => 'application/json',
            ]
        ]);

        return $promise;
    }
}
