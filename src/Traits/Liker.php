<?php

/*
 * This file is part of the overtrue/laravel-like
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelLike\Traits;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Like;

/**
 * Trait Liker.
 */
trait Liker
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @return Like|null
     */
    public function like(Model $object)
    {
        /* @var \Overtrue\LaravelLike\Traits\Likeable $object */
        if (!$this->hasLiked($object)) {
            $like = app(config('like.like_model'));
            $like->{config('like.user_foreign_key')} = $this->getKey();

            $object->likes()->save($like);
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
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->where(config('like.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
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
        return ($this->relationLoaded('likes') ? $this->likes : $this->likes())
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->count() > 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->hasMany(config('like.like_model'), config('like.user_foreign_key'), $this->getKeyName());
    }
}
