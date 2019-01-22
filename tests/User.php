<?php


namespace Tests;


use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\CanLike;

/**
 * Class User
 */
class User extends Model
{
    use CanLike;

    protected $fillable = ['name'];
}