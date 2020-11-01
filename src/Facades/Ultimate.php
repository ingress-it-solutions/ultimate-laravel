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
 * @method Segment startSegment($type, $label)
 * @method mixed addSegment($callback, $type, $label)
 * @method Error reportException(\Throwable $exception, $handled = true)
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
