<?php

namespace EasyTaxi\NewRelic;

use EasyTaxi\NewRelic\Config\TransactionConfig;
use EasyTaxi\NewRelic\Formatter\ArgumentsFormatter;
use EasyTaxi\NewRelic\Formatter\FormatterInterface;

class Transaction
{
    private $instance;
    private $config;
    private $formatter;

    public function __construct($instance, TransactionConfig $config)
    {
        if (!is_object($instance)) {
            throw new \InvalidArgumentException('You need to provide a instance of an object');
        }

        $this->instance = $instance;
        $this->config = $config;
        $this->formatter = new ArgumentsFormatter();

        if (!extension_loaded('newrelic')) {
            throw new \RuntimeException('NewRelic extension is not loaded');
        }
    }

    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    public function __call($name, $arguments)
    {
        newrelic_set_appname($this->config->applicationName);
        newrelic_start_transaction($this->config->applicationName);
        newrelic_name_transaction($this->config->transactionName);
        $customParameters = $this->formatter->format($arguments);
        $this->addNewRelicParameter($customParameters);

        try {
            return call_user_func_array([$this->instance, $name], $arguments);
        } catch (\Exception $genericException) {
            newrelic_notice_error($genericException->getMessage(), $genericException);
            throw $genericException;
        } finally {
            newrelic_end_transaction();
        }
    }

    private function addNewRelicParameter($customParameters)
    {
        foreach ($customParameters as $key => $value) {
            if (null === $value || is_scalar($value)) {
                newrelic_add_custom_parameter($key, $value);
            } else {
                newrelic_add_custom_parameter($key, @json_encode($value));
            }
        }
    }
}
