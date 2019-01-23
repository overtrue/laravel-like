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

/**
 * Trait CanBeLiked.
 */
trait CanLike
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function like(Model $object)
    {
        if (!$this->hasLiked($object)) {
            $like = app(config('like.like_model'));
            $like->{config('like.user_foreign_key')} = $this->getKey();

            return $object->likes()->save($like);
        }

        return true;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return mixed
     */
    public function unlike(Model $object)
    {
        return $object->likes()
            ->where('likable_id', $object->getKey())
            ->where('likable_type', $object->getMorphClass())
            ->delete();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function toggleLike(Model $object)
    {
        if ($this->hasLiked($object)) {
            return $this->unlike($object);
        }

        return $this->like($object);
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
