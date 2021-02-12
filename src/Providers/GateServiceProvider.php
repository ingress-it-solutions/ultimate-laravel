<?php


namespace Ultimate\Laravel\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Ultimate\Laravel\Facades\Ultimate;
use Ultimate\Models\Segment;

class GateServiceProvider extends ServiceProvider
{
    use FetchesStackTrace;

    /**
     * @var Segment []
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::before([$this, 'beforeGateCheck']);
        Gate::after([$this, 'afterGateCheck']);
    }

    /**
     * Intercepting before gate check.
     *
     * @param Authenticatable $user
     * @param string $ability
     */
    public function beforeGateCheck(Authenticatable $user, $ability, $arguments)
    {
        if (Ultimate::isRecording()) {
            $this->segments[
                $this->generateUniqueKey($this->formatArguments($arguments))
            ] = Ultimate::startSegment('gate', 'Authorization:'.$ability);
        }
    }

    /**
     * Intercepting after gate check.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  bool  $result
     * @param  array  $arguments
     * @return bool
     */
    public function afterGateCheck(Authenticatable $user, $ability, $result, $arguments)
    {
        $arguments = $this->formatArguments($arguments);
        $key = $this->generateUniqueKey($arguments);


        if (array_key_exists($key, $this->segments)) {
            $caller = $this->getCallerFromStackTrace();

            $this->segments[$key]
                ->addContext('Check', [
                    'ability' => $ability,
                    'result' => $result ? 'allowed' : 'denied',
                    'arguments' => $arguments,
                    
                ])
                ->end();

                if($caller = $this->getCallerFromStackTrace()) {
                    $this->segments[$key]
                    ->addContext('Caller', [
                        'file' => $caller['file'],
                        'line' => $caller['line'],
                    ]);
                }
        }

        return $result;
    }

    /**
     * Generate a unique key to track segment's state.
     *
     * @param array $data
     * @return string
     */
    public function generateUniqueKey(array $data)
    {
        return md5(json_encode($data));
    }

    /**
     * Format gate arguments.
     *
     * @param array $arguments
     * @return array
     */
    public function formatArguments(array $arguments)
    {
        return array_map(function ($item) {
            return $item instanceof Model ? $this->formatModel($item) : $item;
        }, $arguments);
    }

    /**
     * Human readable model.
     *
     * @param $model
     * @return string
     */
    public function formatModel($model)
    {
        return get_class($model).':'.$model->getKey();
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
