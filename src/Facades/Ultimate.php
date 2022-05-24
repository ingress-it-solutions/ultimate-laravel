<?php

namespace Ultimate\Laravel\Facades;


use Illuminate\Support\Facades\Facade;
use Ultimate\Models\Error;
use Ultimate\Models\Segment;
use Ultimate\Models\Transaction;

/**
 * @method bool isRecording
 * @method Transaction startTransaction($name)
 * @method Transaction currentTransaction()
 * @method static bool needTransaction()
 * @method static bool hasTransaction()
 * @method static bool canAddSegments()
 * @method static bool isRecording()
 * @method static \Ultimate\Ultimate startRecording()
 * @method static \Ultimate\Ultimate stopRecording()
 * @method Segment startSegment($type, $label)
 * @method mixed addSegment($callback, $type, $label, $throw = false)
 * @method Error reportException(\Throwable $exception, $handled = true)
 * @method static void flush()
 * @method static void beforeFlush(callable $callback)
 */
class Ultimate extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return 'ultimate';
    }
}
