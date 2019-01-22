<?php

namespace Overtrue\LaravelLike;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Like8
 */
class Like extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = \config('like.likes_table');

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::saving(function($like){
            $userForeignKey = \config('like.user_foreign_key');
            $like->{$userForeignKey} = $like->{$userForeignKey} ?: auth()->user()->getKey();
        });
    }

    public function likable()
    {
        return $this->morphTo();
    }
}
