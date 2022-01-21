<?php

namespace Overtrue\LaravelLike\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
                return $like->unguarded(function () use ($like, $attributes) {
                    if ($this->relationLoaded('likes')) {
                        $this->unsetRelation('likes');
                    }

                    return $like->create($attributes);
                });
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

    /**
     * Get Query Builder for likes
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function getLikedItems(string $model)
    {
        return app($model)->whereHas(
            'likers',
            function ($q) {
                return $q->where(config('like.user_foreign_key'), $this->getKey());
            }
        );
    }

    public function attachLikeStatus($likeables, callable $resolver = null)
    {
        $returnFirst = false;
        $toArray = false;

        switch (true) {
            case $likeables instanceof Model:
                $returnFirst = true;
                $likeables = \collect([$likeables]);
                break;
            case $likeables instanceof LengthAwarePaginator:
                $likeables = $likeables->getCollection();
                break;
            case $likeables instanceof Paginator:
                $likeables = \collect($likeables->items());
                break;
            case \is_array($likeables):
                $likeables = \collect($likeables);
                $toArray = true;
                break;
        }

        \abort_if(!($likeables instanceof Collection), 422, 'Invalid $likeables type.');

        $liked = $this->likes()->get()->keyBy(function ($item) {
            return \sprintf('%s-%s', $item->likeable_type, $item->likeable_id);
        });

        $likeables->map(function ($likeable) use ($liked, $resolver) {
            $resolver = $resolver ?? fn ($m) => $m;
            $likeable = $resolver($likeable);

            if ($likeable && \in_array(Likeable::class, \class_uses_recursive($likeable))) {
                $key = \sprintf('%s-%s', $likeable->getMorphClass(), $likeable->getKey());
                $likeable->setAttribute('has_liked', $liked->has($key));
            }
        });

        return $returnFirst ? $likeables->first() : ($toArray ? $likeables->all() : $likeables);
    }
}
