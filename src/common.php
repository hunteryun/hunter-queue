<?php

\Hunter\Console::addDefaultCommands([
    "Hunter\\Queue\\command\\Work",
    "Hunter\\Queue\\command\\Restart",
    "Hunter\\Queue\\command\\Listen",
    "Hunter\\Queue\\command\\Subscribe"
]);

if (!function_exists('queue')) {

    /**
     * 添加到队列
     * @param        $job
     * @param string $data
     * @param int    $delay
     * @param null   $queue
     */
    function queue($job, $data = '', $delay = 0, $queue = null)
    {
        if ($delay > 0) {
            \Hunter\Queue::later($delay, $job, $data, $queue);
        } else {
            \Hunter\Queue::push($job, $data, $queue);
        }
    }
}
