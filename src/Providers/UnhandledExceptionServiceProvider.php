<?php


namespace Ultimate\Laravel\Providers;


use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class UnhandledExceptionServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (class_exists(MessageLogged::class)) {
            // starting from L5.4 MessageLogged event class was introduced
            // https://github.com/laravel/framework/commit/57c82d095c356a0fe0f9381536afec768cdcc072
            $this->app['events']->listen(MessageLogged::class, function ($log) {
                $this->handleExceptionLog($log->level, $log->message, $log->context);
            });
        } else {
            $this->app['events']->listen('illuminate.log', function ($level, $message, $context) {
                $this->handleExceptionLog($level, $message, $context);
            });
        }
    }

    protected function handleExceptionLog($level, $message, $context)
    {
        if (
            isset($context['exception']) &&
            ($context['exception'] instanceof \Exception || $context['exception'] instanceof \Throwable)
        ) {
            $this->reportException($context['exception']);
        }

        if ($message instanceof \Exception || $message instanceof \Throwable) {
            $this->reportException($message);
        }

        if ($this->app['ultimate']->isRecording()) {
            $this->app['ultimate']->currentTransaction()
                ->addContext('logs', array_merge(
                    $this->app['ultimate']->currentTransaction()->getContext()['logs']??[],
                    [
                        compact('level', 'message')
                    ]
                ));
        }

    }

    protected function reportException(\Throwable $exception)
    {
        if (!$this->app['ultimate']->isRecording()) {
            $this->app['ultimate']->startTransaction(get_class($exception));
        }

        $this->app['ultimate']->reportException($exception, false);

        $this->app['ultimate']->currentTransaction()->setResult('error');
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
