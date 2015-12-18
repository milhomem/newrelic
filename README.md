New Relic Libraries
==========================

[![Build Status](https://travis-ci.org/easytaxibr/newrelic.svg?branch=master)](https://travis-ci.org/easytaxibr/newrelic)
[![Code Coverage](https://scrutinizer-ci.com/g/easytaxibr/newrelic/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/easytaxibr/newrelic/?branch=master)

New Relic Transaction Library
==========================

Use this library to report background jobs or long running scripts to New Relic APM.

```php
<?php

namespace EmailConsumer;

class EmailConsumer
{
    public function sendEmail($recipient, $body, $header)
    {
        //Send email
    }
}

namespace A\B;

use EasyTaxi\NewRelic;
use EmailConsumer;

$consumer = new EmailConsumer();

while (true) {
    $transactionConfig = new NewRelic\Config\TransactionConfig();
    $transactionConfig->applicationName = 'Background Jobs';
    $transactionConfig->transactionName = 'consumer::sendEmail';
    $consumerMonitored = new NewRelic\Transaction($consumer, $transactionConfig);
    $consumerMonitored->sendEmail('Spock', 'James', 'Tiberius');
}
```

> You MUST have an agent configured and running on the server

New Relic Insights Library
==========================

Use this library to easily post custom events to New Relic Insights.

```php
<?php

use EasyTaxi\NewRelic\Insights;

$client = new Client([
    #You need to change it to your account number
    'base_uri' => 'https://insights-collector.newrelic.com/v1/accounts/99999/'
]);
$this->newRelicInsights = new EasyTaxi\NewRelic\Insights($client, 'YOUR_KEY_HERE');

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
$promise->wait();
```

> You can find your key at Insights https://insights.newrelic.com/accounts/99999/manage/add_data

## Configuring

* Your `base_uri` MUST end with trailing slash `/`
* You MUST replace `99999` with your account number

## Installing

The recommended way to install is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version:

```bash
composer.phar require easytaxibr/newrelic
```
