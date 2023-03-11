<?php

Route::get('version-info', function () {
    return response()->json(phpversion());
})->name('version-info')->middleware((version_compare(app()->version(), '5.6.12') >= 0) ? 'signed' : null);