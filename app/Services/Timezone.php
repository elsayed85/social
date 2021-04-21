<?php

namespace App\Services;

use Carbon\Carbon;

class Timezone
{
    /**
     * @param  Carbon|null  $date
     * @param  null  $format
     * @return Carbon
     */
    public static function convertToLocal($date, $format = 'Y-m-d H:i:s')
    {
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }

        $timezone = (auth()->check() && !is_null(auth()->user()->timezone)) ? auth()->user()->timezone  : config('app.timezone');

        $date->setTimezone($timezone);

        return $date->format($format);
    }

    /**
     * @param $date
     * @return Carbon
     */
    public static function convertFromLocal($date): Carbon
    {
        $timezone = (auth()->check() && !is_null(auth()->user()->timezone)) ? auth()->user()->timezone  : config('app.timezone');
        return Carbon::parse($date, $timezone)->setTimezone('UTC');
    }
}
