<?php

namespace Overtrue\LaravelLike\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Overtrue\LaravelLike\Like;

trait Liker
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return Like
     */
    public function like(Model $object): Like
    {
        $attributes = [
            'likeable_type' => $object->getMorphClass(),
            'likeable_id' => $object->getKey(),
            config('like.user_foreign_key') => $this->getKey(),
        ];

        /* @var \Illuminate\Database\Eloquent\Model $like */
        $like = \app(config('like.like_model'));

        /* @var \Overtrue\LaravelLike\Traits\Likeable|\Illuminate\Database\Eloquent\Model $object */
        return $like->where($attributes)->firstOr(
            function () use ($like, $attributes) {
                $like->unguard();

                if ($this->relationLoaded('likes')) {
                    $this->unsetRelation('likes');
                }

                return $like->create($attributes);
            }
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return bool
     * @throws \Exception
     */
    public function unlike(Model $object): bool
    {
        /* @var \Overtrue\LaravelLike\Like $relation */
        $relation = \app(config('like.like_model'))
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->where(config('like.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            if ($this->relationLoaded('likes')) {
                $this->unsetRelation('likes');
            }

            return $relation->delete();
        }

        return true;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return Like|null
     * @throws \Exception
     */
    public function toggleLike(Model $object)
    {
        return $this->hasLiked($object) ? $this->unlike($object) : $this->like($object);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $object
     *
     * @return bool
     */
    public function hasLiked(Model $object): bool
    {
        return ($this->relationLoaded('likes') ? $this->likes : $this->likes())
                ->where('likeable_id', $object->getKey())
                ->where('likeable_type', $object->getMorphClass())
                ->count() > 0;
    }

    public function likes(): HasMany
    {
        return $this->hasMany(config('like.like_model'), config('like.user_foreign_key'), $this->getKeyName());
    }
}
