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
     */
    public function __construct(Model $like)
    {
        $this->like = $like;
    }
}
