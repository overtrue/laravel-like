<?php

/*
 * This file is part of the overtrue/laravel-like.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\CanBeLiked;

/**
 * Class Post.
 */
class Post extends Model
{
    use CanBeLiked;

    protected $fillable = ['title'];
}
