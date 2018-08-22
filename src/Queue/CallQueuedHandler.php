<?php

namespace Hunter\Queue;

class CallQueuedHandler
{

    public function call(Job $job, array $data)
    {
        $command = unserialize($data['command']);

        call_user_func([$command, 'handle']);

        if (!$job->isDeletedOrReleased()) {
            $job->delete();
        }
    }

    public function failed(array $data)
    {
        $command = unserialize($data['command']);

        if (method_exists($command, 'failed')) {
            $command->failed();
        }
    }
}
