<?php


namespace Ultimate\Laravel\Tests;


use Illuminate\Http\Request;
use Ultimate\Laravel\Facades\Ultimate;
use Ultimate\Laravel\Filters;
use Ultimate\Laravel\Middleware\WebRequestMonitoring;
use Ultimate\Laravel\Tests\Jobs\JobTest;

class FilterClassTest extends BasicTestCase
{
    public function testRequestApproved()
    {
        $this->app->router->get('test', function (Request $request) {
            $this->assertTrue(Filters::isApprovedRequest(config('ultimate.ignore_url'), $request));
        })->middleware(WebRequestMonitoring::class);

        $this->call('GET', 'test');
    }

    public function testRequestNotApproved()
    {
        $this->app->router->get('nova', function (Request $request) {
            $this->assertFalse(Filters::isApprovedRequest(config('ultimate.ignore_url'), $request));
        })->middleware(WebRequestMonitoring::class);

        $this->call('GET', 'nova');
    }

    public function testJobNotApproved()
    {
        $notAllowed = [JobTest::class];

        $this->assertFalse(Filters::isApprovedJobClass(JobTest::class, $notAllowed));

        $this->assertTrue(Filters::isApprovedJobClass(JobTest::class, config('ultimate.ignore_jobs')));

        config()->set('ultimate.ignore_jobs', $notAllowed);

        $this->assertFalse(Filters::isApprovedJobClass(JobTest::class, config('ultimate.ignore_jobs')));
    }
}
