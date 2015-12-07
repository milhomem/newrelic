New Relic Insights Library
==========================

[![Build Status](https://travis-ci.org/easytaxibr/newrelic-insights.svg?branch=master)](https://travis-ci.org/easytaxibr/newrelic-insights)

Use this library to easily post custom events to New Relic Insights.

```php
$client = new Client([
    #You need to change it to your account number
    'base_uri' => 'https://insights-collector.newrelic.com/v1/accounts/99999/'
]);
$this->newRelicInsights = new NewRelic\Insights($client, 'YOUR_KEY_HERE');

$events = new Insights\EventCollection();

$event = new Insights\Event();
$event->eventType = "Purchase";
$event->account = 3;
$event->amount = 259.54;
$events->add($event);

$event2 = new Insights\Event();
$event2->eventType = "Purchase";
$event2->account = 4;
$events->add($event2);

$promise = $this->newRelicInsights->sendEvent($events);
```

> You can find your key at Insights https://insights.newrelic.com/accounts/99999/manage/add_data

## Installing

The recommended way to install is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version:

```bash
composer.phar require easytaxibr/newrelic-insights
```
