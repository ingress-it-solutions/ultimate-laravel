<?php


namespace Ultimate\Laravel\Providers;


use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\ServiceProvider;
use Ultimate\Laravel\Facades\Ultimate;
use Ultimate\Models\Segment;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Segments to monitor.
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
        $this->app['events']->listen(MessageSending::class, function (MessageSending $event) {
            if (Ultimate::isRecording()) {
                $this->segments[
                    $this->generateUniqueKey($event->data)
                ] = Ultimate::startSegment('email', get_class($event->message))->setContext($event->data);
            }
        });

        $this->app['events']->listen(MessageSent::class, function (MessageSent $event) {
            $key = $this->generateUniqueKey($event->data);

            if (array_key_exists($key, $this->segments)) {
                $this->segments[$key]->end();
            }
        });
    }

    /**
     * Generate a unique key to track segment's state.
     *
     * @param array $data
     * @return string
     */
    protected function generateUniqueKey($data): string
    {
        return md5(json_encode($data));
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
