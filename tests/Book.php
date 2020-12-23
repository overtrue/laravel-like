<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;

class Book extends Model
{
    use Likeable;

    protected $fillable = ['title'];
}
