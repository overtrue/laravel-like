<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Overtrue\LaravelLike\Traits\Liker;

class User extends Model
{
    use Liker;
    use Likeable;

    protected $fillable = ['name'];
}
