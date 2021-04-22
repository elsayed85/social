<?php
namespace App\Traits;

use App\Services\Timezone;

trait Verifiable
{
    public function makeVerified()
    {
        return $this->update(['verified_at' => now()]);
    }

    public function makeUnVerified()
    {
        return $this->update(['verified_at' => null]);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeUnVerified($query)
    {
        return $query->whereNull('verified_at');
    }

    public function isVerified()
    {
        return !is_null($this->verified_at);
    }

    public function getVerifiedAtAttribute($value)
    {
        return Timezone::convertToLocal($value);
    }
}
