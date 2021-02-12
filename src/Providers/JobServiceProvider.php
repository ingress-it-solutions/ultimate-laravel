<?php


namespace Ultimate\Laravel\Providers;


use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Ultimate\Laravel\Filters;
use Ultimate\Models\Segment;

class JobServiceProvider extends ServiceProvider
{
    /**
     * Jobs to inspect.
     *
     * @var Segment[]
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Queue::looping(
        //     function () {
        //         $this->app['ultimate']->flush();
        //     }
        // );

        $this->app['events']->listen(
            JobProcessing::class,
            function (JobProcessing $event) {
                // If exists in the job to ignore, return immediately.
                if (
                !Filters::isApprovedJobClass(
                    $event->job->resolveName(),
                    config('ultimate.ignore_jobs')
                )
                ) {
                    return;
                }
                $this->handleJobStart($event->job);

                
            }
        );

        $this->app['events']->listen(
            JobProcessed::class,
            function (JobProcessed $event) {
                $this->handleJobEnd($event->job);
            }
        );

        $this->app['events']->listen(
            JobFailed::class,
            function (JobFailed $event) {
                $this->handleJobEnd($event->job, true);
            }
        );

        $this->app['events']->listen(
            JobExceptionOccurred::class,
            function (JobExceptionOccurred $event) {
                $this->handleJobEnd($event->job, true);
            }
        );
    }

    

    protected function handleJobStart(Job $job)
    {
        if ($this->app['ultimate']->isRecording()) {
            // Open a segment if a transaction already exists
            $this->initializeSegment($job);
        } else {
            // Start a transaction if there's not one
            $this->app['ultimate']
                ->startTransaction($job->resolveName())
                ->addContext('Payload', $job->payload());
        }
    }

    protected function initializeSegment(Job $job)
    {
        $segment = $this->app['ultimate']
            ->startSegment('job', $job->resolveName())
            ->addContext('payload', $job->payload());

        // Jot down the job with a unique ID
        $this->segments[$this->getJobId($job)] = $segment;
    }

    /**
     * Report job execution to Ultimate.
     *
     * @param Job $job
     * @param bool $failed
     */
    public function handleJobEnd(Job $job, $failed = false)
    {
        if (!$this->app['ultimate']->isRecording()) {
            return;
        }

        $id = $this->getJobId($job);

        // If a segment doesn't exists it means that job is registered as transaction
        // we can set the result accordingly
        if (array_key_exists($id, $this->segments)) {
            $this->segments[$id]->end();
        } else {
            $this->app['ultimate']
                ->currentTransaction()
                ->setResult($failed ? 'error' : 'success');
        }
        
        if ($this->app->runningInConsole()) {
            $this->app['inspector']->flush();
        }

    }

    /**
     * Get Job ID.
     *
     * @param Job $job
     * @return string|int
     */
    public static function getJobId(Job $job)
    {
        if ($jobId = $job->getJobId()) {
            return $jobId;
        }

        return sha1($job->getRawBody());
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
