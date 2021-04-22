<?php

namespace App\Models;

use App\Models\Auth\Githup;
use App\Models\Auth\Twitter;
use App\Services\Timezone;
use App\Traits\Blockable;
use App\Traits\LocalTimestamps;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Passport\HasApiTokens;
use Overtrue\LaravelFollow\Followable;

class User extends Authenticatable implements MustVerifyEmail, BannableContract
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Sluggable;
    use Followable;
    use Bannable;
    use Blockable;
    use LocalTimestamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'banned_at',
        'private'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'private' => 'boolean'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'username' => [
                'source' => 'name'
            ]
        ];
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return Timezone::convertToLocal($value);
    }

    public function needsToApproveFollowRequests()
    {
        return (bool) $this->private;
    }


    public function isFollowingAndAccepted($user)
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

        /* @var \Illuminate\Database\Eloquent\Model $this */
        if ($this->relationLoaded('followings')) {
            $requestIsAccepted = optional(optional(
                $this->followings
                    ->where($this->getKeyName(), $user->id)
                    ->first()
            )->pivot)->accepted_at;
            return !is_null($requestIsAccepted);
        }

        $requestIsAccepted = optional(optional($this->followings()
            ->where("following_id", $user->id)
            ->first())
            ->pivot)->accepted_at;
        return !is_null($requestIsAccepted);
    }

    public function isFollowerAndAccepted($user)
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

        if ($this->relationLoaded('followers')) {
            $requestIsAccepted = optional(optional(
                $this->followers
                    ->where($this->getKeyName(), $user->id)
                    ->first()
            )->pivot)->accepted_at;
            return !is_null($requestIsAccepted);
        }

        $requestIsAccepted = optional(optional($this->followers()
            ->where("following_id", $user->id)
            ->first())
            ->pivot)->accepted_at;
        return !is_null($requestIsAccepted);
    }

    public function githup()
    {
        return $this->hasOne(Githup::class);
    }

    public function twitter()
    {
        return $this->hasOne(Twitter::class);
    }
}
