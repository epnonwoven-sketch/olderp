<?php

namespace App\Traits;

use App\Observers\LogObserver;

trait Loggable
{
    public static function bootLoggable()
    {
        static::observe(LogObserver::class);
    }
}
