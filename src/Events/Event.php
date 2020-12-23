<?php

namespace Overtrue\LaravelLike\Events;

use Illuminate\Database\Eloquent\Model;

class Event
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $like;

    /**
     * Event constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $like
     */
    public function __construct(Model $like)
    {
        $this->like = $like;
    }
}
