<?php
namespace Ultimate\Laravel\Tests;
use Ultimate\Laravel\Facades\Ultimate;
use Ultimate\Laravel\Middleware\WebRequestMonitoring;
use Ultimate\Models\Transaction;
class MiddlewareTest extends BasicTestCase
{
    public function testIsRecording()
    {
        $this->assertTrue(Ultimate::isRecording());
        $this->assertTrue(Ultimate::needTransaction());
        $this->app->router->get('test', function () {})
            ->middleware(WebRequestMonitoring::class);
        $this->get('test');
        $this->assertFalse(Ultimate::needTransaction());
        $this->assertInstanceOf(Transaction::class, Ultimate::currentTransaction());
    }
    public function testResult()
    {
// test the middleware
        $this->app->router->get('test', function () {})
            ->middleware(WebRequestMonitoring::class);
        $response = $this->get( 'test');
        $this->assertEquals(
            $response->getStatusCode(),
            Ultimate::currentTransaction()->result
        );
        $this->assertArrayHasKey('Response', Ultimate::currentTransaction()->context);
    }
    public function testContext()
    {
// test the middleware
        $this->app->router->get('test', function () {})
            ->middleware(WebRequestMonitoring::class);
        $this->get( 'test');
        $this->assertArrayHasKey('Request Body', Ultimate::currentTransaction()->context);
        $this->assertArrayHasKey('Response', Ultimate::currentTransaction()->context);
    }
}