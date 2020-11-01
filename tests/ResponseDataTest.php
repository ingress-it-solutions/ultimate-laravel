<?php


namespace Ultimate\Laravel\Tests;


use Ultimate\Laravel\Facades\Ultimate;
use Ultimate\Laravel\Middleware\WebRequestMonitoring;

class ResponseDataTest extends BasicTestCase
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
    }

    public function testResult()
    {
        // test the middleware
        $this->app->router->get('test/{param}', function () {
            $this->assertTrue(Ultimate::isRecording());
            $this->assertEquals('request', Ultimate::currentTransaction()->type);
            $this->assertStringContainsString('GET /test/{param}', Ultimate::currentTransaction()->name);
        })->middleware(WebRequestMonitoring::class);

        $response = $this->call('GET', 'test/param');

        // Test result
        $this->assertEquals(
            $response->getStatusCode(),
            Ultimate::currentTransaction()->result
        );

        //Test response
        $this->assertArrayHasKey('Response', Ultimate::currentTransaction()->context);
    }
}
