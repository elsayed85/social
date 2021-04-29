<?php

namespace App\Models;

use App\Models\Auth\Githup;
use App\Models\Auth\Twitter;
use App\Models\Posts\Post;
use App\Services\Timezone;
use App\Traits\Blockable;
use App\Traits\LocalTimestamps;
use App\Traits\Verifiable;
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
use Nicolaslopezj\Searchable\SearchableTrait;
use Overtrue\LaravelFollow\Followable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements MustVerifyEmail, BannableContract, HasMedia
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
    use Verifiable;
    use SearchableTrait;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'email_verified_at',
        'timezone',
        'banned_at',
        'private',
        'verified_at'
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
        'private' => 'boolean',
        'verified_at' => 'datetime'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar',
        'cover',
        'two_factor_auth_enabled'
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'users.name' => 10,
            'users.username' => 2,
            'users.email' => 5,
            'posts.content' => 12,
        ],
        'joins' => [
            'posts' => ['users.id', 'posts.user_id'],
        ],
    ];

    public const AVATAR_SIZE = 3072; // 3MB


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

    public static function getImageValidationRules()
    {
        return ['image/jpeg', 'image/png', 'image/jpg'];
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->acceptsMimeTypes(static::getImageValidationRules())
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('avatar-thumb-x1')
                    ->width(100)
                    ->height(100)
                    ->nonQueued();
                $this
                    ->addMediaConversion('avatar-thumb-x2')
                    ->width(200)
                    ->height(200)
                    ->nonQueued();
            });

        $this
            ->addMediaCollection('cover')
            ->acceptsMimeTypes(static::getImageValidationRules())
            ->singleFile();
    }

    public function getAvatarAttribute()
    {
        $avatar = $this->getFirstMedia('avatar');
        if (!is_null($avatar)) {
            return collect([
                'original' => $avatar->getFullUrl(),
                'thumps' => [
                    'x1' => $avatar->getUrl('avatar-thumb-x1'),
                    'x2' => $avatar->getUrl('avatar-thumb-x2')
                ]
            ]);
        }
        $name = str_replace(' ', '+', $this->name);
        return collect(['original' => "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF"]);
    }

    public function getCoverAttribute()
    {
        $avatar = $this->getFirstMedia('cover');
        if (!is_null($avatar)) {
            return $avatar->getFullUrl();
        }
        return "https://picsum.photos/1500/500";
    }

    public function getTwoFactorAuthEnabledAttribute()
    {
        return !is_null($this->two_factor_secret);
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return Timezone::convertToLocal($value);
    }

    public function needsToApproveFollowRequests()
    {
        return $this->private;
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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
