<?php

/*
 * This file is part of the overtrue/laravel-like.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Overtrue\LaravelLike\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Overtrue\LaravelLike\Events\Liked;
use Overtrue\LaravelLike\Events\Unliked;
use Overtrue\LaravelLike\Like;

/**
 * Trait CanBeLiked.
 */
trait CanLike
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @return Like|null
     */
    public function like(Model $object)
    {
        if (!$this->hasLiked($object)) {
            $like = app(config('like.like_model'));
            $like->{config('like.user_foreign_key')} = $this->getKey();

            $like = $object->likes()->save($like);

            Event::dispatch(new Liked($this, $object));

            return $like;
        }

        return null;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @return null
     */
    public function unlike(Model $object)
    {
        $relation = $object->likes()
            ->where('likable_id', $object->getKey())
            ->where('likable_type', $object->getMorphClass())
            ->where(config('like.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
            Event::dispatch(new Unliked($this, $object));
        }

        return null;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @return Like|null
     */
    public function toggleLike(Model $object)
    {
        return $this->hasLiked($object) ? $this->unlike($object) : $this->like($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasLiked(Model $object)
    {
        return tap($this->relationLoaded('likes') ? $this->likes : $this->likes())
            ->where('likable_id', $object->getKey())
            ->where('likable_type', $object->getMorphClass())
            ->count() > 0;
    }

    /**
     * Return like.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->hasMany(config('like.like_model'), config('like.user_foreign_key'), $this->getKeyName());
    }

    /**
     * @param string|null $model
     *
     * @return mixed
     */
    public function likedItems(string $model = null)
    {
        $this->load(['likes' => function ($query) use ($model) {
            $model && $query->where('likable_type', app($model)->getMorphClass());
        }, 'likes.likable']);

        return $this->likes->pluck('likable');
    }
}
