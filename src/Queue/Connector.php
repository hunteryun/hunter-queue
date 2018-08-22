<?php

namespace Hunter\Queue;

use InvalidArgumentException;

abstract class Connector
{
    protected $options = [];

    abstract public function push($job, $data = '', $queue = null);

    abstract public function later($delay, $job, $data = '', $queue = null);

    abstract public function pop($queue = null);

    public function marshal()
    {
        throw new \RuntimeException('pop queues not support for this type');
    }

    protected function createPayload($job, $data = '', $queue = null)
    {
        if (is_object($job)) {
            $payload = json_encode([
                'job'  => 'Hunter\Queue\CallQueuedHandler@call',
                'data' => [
                    'commandName' => get_class($job),
                    'command'     => serialize(clone $job),
                ],
            ]);
        } else {
            $payload = json_encode($this->createPlainPayload($job, $data));
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException('Unable to create payload: ' . json_last_error_msg());
        }

        return $payload;
    }

    protected function createPlainPayload($job, $data)
    {
        return ['job' => $job, 'data' => $data];
    }

    protected function setMeta($payload, $key, $value)
    {
        $payload       = json_decode($payload, true);
        $payload[$key] = $value;
        $payload       = json_encode($payload);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException('Unable to create payload: ' . json_last_error_msg());
        }

        return $payload;
    }
}
