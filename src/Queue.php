<?php

namespace Digitonic\Queues;

final class Queue
{
    public static function __callStatic($name, $arguments)
    {
        return config('digitonic.queues.'.$name);
    }
}
