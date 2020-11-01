<?php


namespace Ultimate\Laravel\Tests;


use Ultimate\Ultimate;

class HelpersTest extends BasicTestCase
{
    public function testGenerateInstance()
    {
        $this->assertInstanceOf(Ultimate::class, \ultimate());
    }
}
