<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Githup extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['token'];

    public function getAvatarAttribute()
    {
        return "https://avatars.githubusercontent.com/u/{$this->githup_id}?v=4";
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
