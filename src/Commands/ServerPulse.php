<?php


namespace Ultimate\Laravel\Commands;


use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class ServerPulse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultimate:pulse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect server resources consumption.';


    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        if (ultimate()->hasTransaction() && ultimate()->isRecording()) {
            ultimate()->currentTransaction()->sampleServerStatus(1);
        }
    }
}