<?php

namespace EasyTaxi\NewRelic;

class Transaction
{
    private $instance;
    private $appName;

    public function __construct($instance, $appName)
    {
        if (!is_object($instance)) {
            throw new \InvalidArgumentException('You need to provide a instance of an object');
        }

        $this->instance = $instance;
        $this->appName = $appName;

        if (!extension_loaded('newrelic')) {
            throw new \RuntimeException('NewRelic extension is not loaded');
        }
    }

    public function __call($name, $arguments)
    {
        newrelic_start_transaction($this->appName);
        $transactionName = sprintf('%s::%s', get_class($this->instance), $name);
        newrelic_name_transaction($transactionName);
        $this->sendArgumentsToNewRelic($arguments);

        try {
            return call_user_func_array([$this->instance, $name], $arguments);
        } catch (\Exception $genericException) {
            newrelic_notice_error($genericException->getMessage(), $genericException);
            throw $genericException;
        } finally {
            newrelic_end_transaction();
        }
    }

    private function sendArgumentsToNewRelic(array $arguments)
    {
        foreach ($arguments as $key => $value) {
            if (null === $value || is_scalar($value)) {
                $argument = [$key => $value];
            } else {
                $argument = $value;
            }
            $this->addNewRelicParameter($argument);
        }
    }

    private function addNewRelicParameter($argument)
    {
        foreach ($argument as $key => $value) {
            if (null === $value || is_scalar($value)) {
                newrelic_add_custom_parameter($key, $value);
            } else {
                newrelic_add_custom_parameter($key, @json_encode($value));
            }
        }
    }
}
