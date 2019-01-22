<?php


namespace Overtrue\LaravelLike\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait CanBeLiked
 */
trait CanBeLiked
{
    public function isLikedBy(Model $user)
    {
        if (\is_a($user, config('like.user_model'))) {
            return $this->likers->where($user->getKeyName(), $user->getKey())->count() > 0;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function likes()
    {
        return $this->morphMany(config('like.like_model'), 'likable');
    }

    /**
     * Return followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likers()
    {
        return $this->belongsToMany(config('like.user_model'), config('like.likes_table'), 'likable_id', config('like.user_foreign_key'))
            ->where('likable_type', static::class);
    }
}
