<?php


namespace Overtrue\LaravelLike\Traits;


use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Like;

/**
 * Trait CanBeLiked
 */
trait CanLike
{
    public function like(Model $object)
    {
        if (!$this->hasLiked($object)) {
            $like = app(config('like.like_model'));
            $like->{config('like.user_foreign_key')} = $this->getKey();
            return $object->likes()->save($like);
        }

        return true;
    }

    public function unlike(Model $object)
    {
        return $object->likes()
            ->where('likable_id', $object->getKey())
            ->where('likable_type', $object->getMorphClass())
            ->delete();
    }

    public function toggleLike(Model $object)
    {
        if ($this->hasLiked($object)) {
            return $this->unlike($object);
        }

        return $this->like($object);
    }

    public function hasLiked(Model $object)
    {
        return $this->likes
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
}
