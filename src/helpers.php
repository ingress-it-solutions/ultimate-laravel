<?php

if (!function_exists('ultimate')) {
    /**
     * @return \Ultimate\Laravel\Ultimate
     */
    function ultimate(): \Ultimate\Laravel\Ultimate
    {
        return app('ultimate');
    }
}
