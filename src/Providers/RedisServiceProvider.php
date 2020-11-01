<?php


namespace Ultimate\Laravel\Providers;


use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\ServiceProvider;
use Ultimate\Laravel\Facades\Ultimate;

class RedisServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->listen(CommandExecuted::class, function (CommandExecuted $event) {
            if (Ultimate::isRecording()) {
                // milliseconds to microseconds
                $microtimeDuration = $event->time/1000;

                Ultimate::startSegment('redis', "redis:{$event->command}")
                    ->start(microtime(true) - $microtimeDuration)
                    ->addContext('data', [
                        'connection' => $event->connectionName,
                        'parameters' => $event->parameters
                    ])
                    ->end($microtimeDuration);
            }
        });

        foreach ((array) $this->app['redis']->connections() as $connection) {
            $connection->setEventDispatcher($this->app['events']);
        }

        $this->app['redis']->enableEvents();
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
