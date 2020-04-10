<?php

/*
 * This file is part of the overtrue/laravel-like
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
