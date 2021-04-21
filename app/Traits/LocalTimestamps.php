<?php

namespace App\Traits;

use App\Services\Timezone;

trait LocalTimestamps
{
    public function getCreatedAtAttribute($value)
    {
        return Timezone::convertToLocal($value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return Timezone::convertToLocal($value);
    }
}
