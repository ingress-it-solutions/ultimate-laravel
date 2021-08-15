<?php


namespace Ultimate\Laravel\Tests;


class ConfigurationTest extends BasicTestCase
{
    public function testMaxItems()
    {
        $this->assertSame(150, (int) config('ultimate.max_items'));
    }

    public function testKey()
    {
        $this->assertEquals('xxx', config('ultimate.key'));
    }
}
