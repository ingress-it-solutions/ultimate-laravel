<?php


namespace Ultimate\Laravel\Commands;


use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultimate:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send data to your Ultimate dashboard.';

    /**
     * Execute the console command.
     *
     * @param Repository $config
     * @return void
     * @throws \Throwable
     */
    public function handle(Repository $config)
    {
        $this->line("I'm testing your Ultimate integration.");

        // Check Ultimate API key
        ultimate()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            !empty($config->get('ultimate.key'))
                ? $this->info('✅ Ultimate key installed.')
                : $this->warn('❌ Ultimate key not specified. Make sure you specify a value in the `key` field of the `ultimate` config file.');

            $segment->addContext('example payload', ['foo' => 'bar']);
        }, 'test', 'Check API key');

        // Check Ultimate is enabled
        ultimate()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            $config->get('ultimate.enable')
                ? $this->info('✅ Ultimate is enabled.')
                : $this->warn('❌ Ultimate is actually disabled, turn to true the `enable` field of the `ultimate` config file.');

            $segment->addContext('another payload', ['foo' => 'bar']);
        }, 'test', 'Check if Ultimate is enabled');

        // Check CURL
        ultimate()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            function_exists('curl_version')
                ? $this->info('✅ CURL extension is enabled.')
                : $this->warn('❌ CURL is actually disabled so your app could not be able to send data to Ultimate.');

            $segment->addContext('another payload', ['foo' => 'bar']);
        }, 'test', 'Check CURL extension');

        // Report Exception
        ultimate()->reportException(new \Exception('First Exception detected'));

        ultimate()->currentTransaction()->setResult('success')->end();

        // A demo transaction
        ultimate()->startTransaction("artisan {$this->signature}")
            ->start(microtime(true) - 60*5)
            ->setResult('success')
            ->end(200);

        $this->line('Done!');
    }
}
