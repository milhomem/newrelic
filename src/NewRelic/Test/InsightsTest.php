<?php

namespace NewRelic\Test;

use NewRelic;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise;
use GuzzleHttp\Middleware;
use NewRelic\Entity\Insights;

class InsightsTest extends \PHPUnit_Framework_TestCase
{
    private $newRelicInsights;
    private $requestContainer;

    public function setUp()
    {
        $this->requestContainer = [];
        $history = Middleware::history($this->requestContainer);
        $mock = new MockHandler([new Response(200, [])]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $client = new Client([
            'handler' => $handler,
            'base_uri' => 'http://WhoCares'
        ]);
        $this->newRelicInsights = new NewRelic\Insights($client, 'Mum-Ha');
    }

    public function testCanSendAsyncRequest()
    {
        $promise = $this->newRelicInsights->sendEvent(new Insights\EventCollection());

        $response = $promise->wait();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testFulfillMessageInterface()
    {
        $event = new Insights\Event();
        $event->eventType = "Purchase";
        $event->account = 3;
        $event->amount = 259.54;
        $events = new Insights\EventCollection();
        $events->add($event);

        $promise = $this->newRelicInsights->sendEvent($events);

        $promise->wait();
        $request = $this->requestContainer[0]['request'];
        $this->assertEquals('[{"eventType":"Purchase","account":3,"amount":259.54}]', $request->getBody()->getContents());
    }

    public function provideInvalidEventTypes()
    {
        return [
            'no type defined' => [null],
            'type as integer' => [123],
            'empty type' => [''],
        ];
    }

    /**
     * @dataProvider provideInvalidEventTypes
     * @expectedException Exception
     */
    public function testEventType($type)
    {
        $event = new Insights\Event();
        $event->eventType = $type;
        $events = new Insights\EventCollection();
        $events->add($event);

        $this->newRelicInsights->sendEvent($events);
    }
}
