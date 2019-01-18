<?php


namespace Overtrue\LaravelLike\Traits;


use Overtrue\LaravelLike\Like;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait CanBeLiked
 *
 * @author overtrue <i@overtrue.me>
 */
trait CanLike
{
    public function like(Model $object)
    {
        if (!$this->hasLiked($object)) {
            $like = new ${config('like.like_model')}(
                [config('like.user_id_foreign_key', 'user_id') => $this->id]
            );
            return $object->likable()->save($like);
        }

        return true;
    }

    public function unlike(Model $object)
    {
        return $object->likable()
            ->where('likable_id', $object->id)
            ->where('likable_type', \get_class($object))
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
                ->where('likable_id', $object->id)
                ->where('likable_type', \get_class($object))
                ->count() > 0;
    }

    /**
     * Return like.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
