<?php

namespace EasyTaxi\NewRelic\Test\Stub;

class Foo
{
    public function bar($argument = null)
    {
        return $argument;
    }

    public function fooThrowThisException(\Exception $exception)
    {
        throw $exception;
    }
}
