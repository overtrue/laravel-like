<?php


namespace App\Traits;


use App\Like;
use App\User;

/**
 * Trait CanBeLiked
 *
 * @author overtrue <i@overtrue.me>
 */
trait CanBeLiked
{
    public function isLikedBy(User $user)
    {
        return $this->likers->where('id', $user->id)->count() > 0;
    }

    /**
     * @return mixed
     */
    public function likable()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    /**
     * Return followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likers()
    {
        return $this->belongsToMany(User::class, 'likes', 'likable_id', 'user_id')
            ->where('likable_type', static::class);
    }
}
