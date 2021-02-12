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
        if (!ultimate()->isRecording()) {
            $this->warn('Ultimate is not enabled');
            return;
        }

        $this->line("I'm testing your Ultimate integration.");

        // Check Ultimate API key
        ultimate()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            !empty($config->get('ultimate.key'))
                ? $this->info('✅ Ultimate key installed.')
                : $this->warn('❌ Ultimate key not specified. Make sure you specify the ULTIMATE_BUGTRAP_KEY in your .env file.');

            $segment->addContext('example payload', ['key' => $config->get('ultimate.key')]);
        }, 'test', 'Check Bugtrap key');

        // Check Ultimate is enabled
        ultimate()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            $config->get('ultimate.enable')
                ? $this->info('✅ Ultimate is enabled.')
                : $this->warn('❌ Ultimate is actually disabled, turn to true the `enable` field of the `ultimate` config file.');

            $segment->addContext('another payload', ['enable' => $config->get('ultimate.enable')]);
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
        // End the transaction
        ultimate()->currentTransaction()
            ->setResult('error')
            ->end();

        // Demo data
        foreach ([1, 2, 3, 4, 5, 6] as $minutes) {
            ultimate()->startTransaction("artisan {$this->signature}")
                ->start(microtime(true) - 1*$minutes)
                ->setResult('success')
                ->end(rand(100, 200));

            ultimate()->addSegment(function () {
                usleep(rand(10, 50) * 10);
            }, 'segment', 'Task performance');

            // Logs will be reported in the transaction context.
            \Log::debug("Here you'll find log entries generated during the transaction.");
        }

        $this->line('Done!');
    }
}