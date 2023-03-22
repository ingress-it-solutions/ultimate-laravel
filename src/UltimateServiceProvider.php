<?php

namespace Ultimate\Laravel;


use Illuminate\Contracts\View\Engine;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as ViewFactory;
use Ultimate\Laravel\Commands\PublishCommand;
use Ultimate\Laravel\Commands\TestCommand;
use Ultimate\Laravel\Commands\UltimatePackageInfoCommand;
use Ultimate\Laravel\Providers\CommandServiceProvider;
use Ultimate\Laravel\Providers\DatabaseQueryServiceProvider;
use Ultimate\Laravel\Providers\EmailServiceProvider;
use Ultimate\Laravel\Providers\GateServiceProvider;
use Ultimate\Laravel\Providers\JobServiceProvider;
use Ultimate\Laravel\Providers\NotificationServiceProvider;
use Ultimate\Laravel\Providers\RedisServiceProvider;
use Ultimate\Laravel\Providers\ExceptionServiceProvider;
use Ultimate\Laravel\Views\ViewEngineDecorator;
use Laravel\Lumen\Application as LumenApplication;
use Ultimate\Configuration;
use Ultimate\Laravel\Providers\HttpClientServiceProvider;
use Illuminate\Console\Scheduling\Schedule;


class UltimateServiceProvider extends ServiceProvider
{
    /**
     * The latest version of the client library.
     *
     * @var string
     */
    const VERSION = '23.03.22';

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {

        $this->setupConfigFile();

        if ($this->app->runningInConsole()) {
            $this->commands([
                TestCommand::class,
                UltimatePackageInfoCommand::class

            ]);
        }
    }

    /**
     * Setup configuration file.
     */
    protected function setupConfigFile()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/ultimate.php' => config_path('ultimate.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('ultimate');
        }
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('ultimate:package-info')->hourly();
        });

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Default package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/ultimate.php', 'ultimate');

        // Bind Ultimate service class
        $this->app->singleton('ultimate', function ($app) {
            $configuration = (new Configuration(config('ultimate.key')))
                ->setEnabled(config('ultimate.enable', true))
                ->setUrl(config('ultimate.url'))
                ->setVersion(self::VERSION)
                ->setTransport(config('ultimate.transport', 'async'))
                ->setOptions(config('ultimate.options', []))
                ->setMaxItems(config('ultimate.max_items', 100));

            return new Ultimate($configuration);
        });

        $this->registerUltimateServiceProviders();


    }

    /**
     * Decorate View engine to monitor view rendering performance.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function bindViewEngine(): void
    {
        $viewEngineResolver = function (EngineResolver $engineResolver): void {
            foreach (['file', 'php', 'blade'] as $engineName) {
                $realEngine = $engineResolver->resolve($engineName);

                $engineResolver->register($engineName, function () use ($realEngine) {
                    return $this->wrapViewEngine($realEngine);
                });
            }
        };

        if ($this->app->resolved('view.engine.resolver')) {
            $viewEngineResolver($this->app->make('view.engine.resolver'));
        } else {
            $this->app->afterResolving('view.engine.resolver', $viewEngineResolver);
        }
    }

    private function wrapViewEngine(Engine $realEngine): Engine
    {
        /** @var ViewFactory $viewFactory */
        $viewFactory = $this->app->make('view');

        $viewFactory->composer('*', static function (View $view) use ($viewFactory): void {
            $viewFactory->share(ViewEngineDecorator::SHARED_KEY, $view->name());
        });

        return new ViewEngineDecorator($realEngine, $viewFactory);
    }

    /**
     * Bind Ultimate service providers based on package configuration.
     */
    public function registerUltimateServiceProviders()
    {

        $this->app->register(CommandServiceProvider::class);


        $this->app->register(GateServiceProvider::class);

        // For Laravel >=6
        if (config('ultimate.redis', true) && substr(app()->version(), 0, 1) > 5) {
            $this->app->register(RedisServiceProvider::class);
        }

        if (config('ultimate.unhandled_exceptions', true)) {
            $this->app->register(ExceptionServiceProvider::class);
        }

        if(config('ultimate.query', true)){
            $this->app->register(DatabaseQueryServiceProvider::class);
        }

        if (config('ultimate.job', true)) {
            $this->app->register(JobServiceProvider::class);
        }

        if (config('ultimate.email', true)) {
            $this->app->register(EmailServiceProvider::class);
        }

        if (config('ultimate.notifications', true)) {
            $this->app->register(NotificationServiceProvider::class);
        }

        // Compatibility with Laravel < 8.4
        if (
            config('ultimate.http_client', true) &&
            class_exists('\Illuminate\Http\Client\Events\RequestSending') &&
            class_exists('\Illuminate\Http\Client\Events\ResponseReceived')
        ) {
            $this->app->register(HttpClientServiceProvider::class);
        }


        if (config('ultimate.views')) {
            $this->bindViewEngine();
        }
    }
}
