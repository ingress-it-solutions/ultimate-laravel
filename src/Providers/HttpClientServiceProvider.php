<?php


namespace Ultimate\Laravel\Providers;


use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;
use Illuminate\Support\ServiceProvider;
use Ultimate\Laravel\Facades\Ultimate;


class HttpClientServiceProvider extends ServiceProvider
{
    /**
     * Segments collection.
     *
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
        $this->app['events']->listen(RequestSending::class, function (RequestSending $event) {
            if (Ultimate::canAddSegments()) {
                $this->segments[$this->getSegmentKey($event->request)] = Ultimate::startSegment('http', $event->request->url());
            }
        });

        $this->app['events']->listen(ResponseReceived::class, function (ResponseReceived $event) {
            $key = $this->getSegmentKey($event->request);

            if (array_key_exists($key, $this->segments)) {
                $this->segments[$key]->end()
                    ->addContext('Url', [
                        'method' => $event->request->method(),
                        'url' => $event->request->url(),
                    ])
                    ->addContext('Request', [
                        'type' => $event->request->isForm() ? 'form' : ($event->request->isJson() ? 'json' : 'unknown'),
                        'data' => $event->request->data(),
                        'headers' => $event->request->headers(),
                    ])
                    ->addContext('Response', [
                        'status' => $event->response->status(),
                        'headers' => $event->response->headers(),
                    ])
                    ->label = $event->response->status() . ' ' . $event->request->method() . ' ' . $event->request->url();
            }
        });
    }

    /**
     * Generate the key to identify the segment in the segment collection.
     *
     * @param Request $request
     * @return string
     */
    protected function getSegmentKey(Request $request)
    {
        return sha1($request->body());
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