<?php


namespace Ultimate\Laravel\Tests;


use Ultimate\Laravel\Providers\DatabaseQueryServiceProvider;
use Ultimate\Laravel\Providers\EmailServiceProvider;
use Ultimate\Laravel\Providers\JobServiceProvider;
use Ultimate\Laravel\Providers\NotificationServiceProvider;
use Ultimate\Laravel\Providers\RedisServiceProvider;
use Ultimate\Laravel\Providers\UnhandledExceptionServiceProvider;

class DisablingProvidersTest extends BasicTestCase
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('ultimate.job', false);
        $app['config']->set('ultimate.query', false);
        $app['config']->set('ultimate.email', false);
        $app['config']->set('ultimate.notifications', false);
        $app['config']->set('ultimate.unhandled_exceptions', false);
        $app['config']->set('ultimate.redis', false);
    }

    public function testBindingDisabled()
    {
        // Bind Ultimate service
        $this->assertInstanceOf(\Ultimate\Ultimate::class, $this->app['ultimate']);

        // Nor register service providers
        $this->assertNull($this->app->getProvider(JobServiceProvider::class));
        $this->assertNull($this->app->getProvider(DatabaseQueryServiceProvider::class));
        $this->assertNull($this->app->getProvider(EmailServiceProvider::class));
        $this->assertNull($this->app->getProvider(NotificationServiceProvider::class));
        $this->assertNull($this->app->getProvider(UnhandledExceptionServiceProvider::class));
        $this->assertNull($this->app->getProvider(RedisServiceProvider::class));
    }
}
