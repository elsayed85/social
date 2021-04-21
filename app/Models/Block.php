<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $dates = ['blocked_at'];


    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }


    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by_id');
    }
}
