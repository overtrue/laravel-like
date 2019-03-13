<?php

/*
 * This file is part of the overtrue/laravel-like.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Overtrue\LaravelLike\Events;

use Illuminate\Database\Eloquent\Model;

class Event
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $user;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $target;

    /**
     * Event constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param \Illuminate\Database\Eloquent\Model $target
     */
    public function __construct(Model $user, Model $target)
    {
        $this->user = $user;
        $this->target = $target;
    }
}
