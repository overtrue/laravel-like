<?php

namespace Overtrue\LaravelLike;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        self::saving(function($like){
            $like->user_id = $like->user_id ?: auth()->id();
        });
    }

    public function likable()
    {
        return $this->morphTo();
    }
}
