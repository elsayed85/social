<?php

namespace App\Models\Posts;

use App\Models\User;
use App\Traits\Loveable;
use App\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use Publishable;
    use Loveable;
    use SearchableTrait;
    use InteractsWithMedia;


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'published_at'
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
            'users.name' => 2,
            'users.username' => 2,
            'users.email' => 1,
            'posts.content' => 3,
        ],
        'joins' => [
            'users' => ['users.id', 'posts.user_id'],
        ],
    ];

    public static function getVideoValidationRules()
    {
        return ['video/mp4', 'video/avi'];
    }

    public static function getImageValidationRules()
    {
        return ['image/jpeg', 'image/png', 'image/jpg'];
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('pimg')
            ->acceptsMimeTypes(static::getImageValidationRules())
            ->onlyKeepLatest(4)
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('pimg-thump')
                    ->width(100)
                    ->height(100);
            });

        $this
            ->addMediaCollection('pvid')
            ->acceptsMimeTypes(static::getVideoValidationRules())
            ->onlyKeepLatest(2);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
