<?php


namespace Ultimate\Laravel\Providers;


use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\ServiceProvider;
use Ultimate\Laravel\Facades\Ultimate;
use Ultimate\Laravel\Filters;
use Symfony\Component\Console\Input\ArgvInput;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {

            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }


            if (Ultimate::needTransaction()) {
                Ultimate::startTransaction($event->command)
                    ->addContext('Command', [
                        'arguments' => $event->input->getArguments(),
                        'options' => $event->input->getOptions(),
                    ]);
            } elseif (Ultimate::canAddSegments()) {
                $this->segments[$event->command] = Ultimate::startSegment('artisan', $event->command);
            }
        });
        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {

            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }
            if(Ultimate::hasTransaction() && Ultimate::currentTransaction()->name === $event->command) {
                Ultimate::currentTransaction()->setResult($event->exitCode === 0 ? 'success' : 'error');
            } elseif(array_key_exists($event->command, $this->segments)) {
                $this->segments[$event->command]->end()->addContext('Command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
            }

        });
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

    /**
     * Determine if the current command should be monitored.
     *
     * @param string $command
     * @return bool
     */
    protected function shouldBeMonitored(?string $command): bool
    {
        if(is_string($command)) {
            return Filters::isApprovedArtisanCommand($command, config('ultimate.ignore_commands'));
        }
        return false;
    }
}
