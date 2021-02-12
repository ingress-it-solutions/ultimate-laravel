<?php


namespace Ultimate\Laravel\Providers;


use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app['ultimate']->isRecording()) {
            $this->app['ultimate']->startTransaction(implode(' ', $_SERVER['argv']));
        }

        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {
            if($this->app['inspector']->isRecording()) {
                $this->app['ultimate']->currentTransaction()
                    ->addContext('Command', [
                        'exit_code' => $event->exitCode,
                        'arguments' => $event->input->getArguments(),
                        'options' => $event->input->getOptions(),
                    ])->setResult($event->exitCode === 0 ? 'success' : 'error');
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
}
