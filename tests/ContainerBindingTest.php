<?php


namespace Ultimate\Laravel\Tests;


use Ultimate\Laravel\Providers\DatabaseQueryServiceProvider;
use Ultimate\Laravel\Providers\EmailServiceProvider;
use Ultimate\Laravel\Providers\GateServiceProvider;
use Ultimate\Laravel\Providers\JobServiceProvider;
use Ultimate\Laravel\Providers\NotificationServiceProvider;
use Ultimate\Laravel\Providers\RedisServiceProvider;
use Ultimate\Laravel\Providers\UnhandledExceptionServiceProvider;

class ContainerBindingTest extends BasicTestCase
{
    public function testBinding()
    {
        // Bind Ultimate service
        $this->assertInstanceOf(\Ultimate\Ultimate::class, $this->app['ultimate']);

        // Register service providers
        $this->assertInstanceOf(GateServiceProvider::class, $this->app->getProvider(GateServiceProvider::class));
        $this->assertInstanceOf(RedisServiceProvider::class, $this->app->getProvider(RedisServiceProvider::class));
        $this->assertInstanceOf(EmailServiceProvider::class, $this->app->getProvider(EmailServiceProvider::class));
        $this->assertInstanceOf(JobServiceProvider::class, $this->app->getProvider(JobServiceProvider::class));
        $this->assertInstanceOf(NotificationServiceProvider::class, $this->app->getProvider(NotificationServiceProvider::class));
        $this->assertInstanceOf(UnhandledExceptionServiceProvider::class, $this->app->getProvider(UnhandledExceptionServiceProvider::class));
        $this->assertInstanceOf(DatabaseQueryServiceProvider::class, $this->app->getProvider(DatabaseQueryServiceProvider::class));
    }
}
