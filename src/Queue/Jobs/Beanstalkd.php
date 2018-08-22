<?php

namespace Hunter\Queue\Jobs;

use Hunter\Queue\Job;
use Hunter\Queue\connector\Beanstalkd as BeanstalkdQueue;

class Beanstalkd extends Job
{

    /**
     * The Iron queue instance.
     *
     * @var BeanstalkdQueue
     */
    protected $beanstalkd;

    /**
     * The IronMQ message instance.
     *
     * @var object
     */
    protected $job;

    public function __construct(BeanstalkdQueue $beanstalkd, $job, $queue)
    {
        $this->beanstalkd      = $beanstalkd;
        $this->job           = $job;
        $this->queue         = $queue;
        $this->job->attempts = $this->job->attempts + 1;
    }

    /**
     * Fire the job.
     * @return void
     */
    public function fire()
    {
        $this->resolveAndFire(json_decode($this->job->payload, true));
    }

    /**
     * Get the number of times the job has been attempted.
     * @return int
     */
    public function attempts()
    {
        return (int) $this->job->attempts;
    }

    public function delete()
    {
        parent::delete();

        $this->beanstalkd->deleteMessage($this->queue, $this->job->id);
    }

    public function release($delay = 0)
    {
        parent::release($delay);

        $this->delete();

        $this->beanstalkd->release($this->queue, $this->job, $delay);
    }

    /**
     * Get the raw body string for the job.
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->payload;
    }

}
