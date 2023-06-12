<?php

namespace Overtrue\LaravelLike;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Overtrue\LaravelLike\Events\Liked;
use Overtrue\LaravelLike\Events\Unliked;

class Like extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => Liked::class,
        'deleted' => Unliked::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = \config('like.likes_table');

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::saving(function ($like) {
            $userForeignKey = \config('like.user_foreign_key');
            $like->{$userForeignKey} = $like->{$userForeignKey} ?: auth()->id();

            if (\config('like.uuids')) {
                $like->{$like->getKeyName()} = $like->{$like->getKeyName()} ?: (string) Str::orderedUuid();
            }
        });
    }

    public function likeable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $userModel = config('like.user_model') ?? config('auth.providers.users.model');

        return $this->belongsTo($userModel, \config('like.user_foreign_key'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function liker()
    {
        return $this->user();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('likeable_type', app($type)->getMorphClass());
    }
}
