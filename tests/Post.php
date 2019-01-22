<?php


namespace Tests;


use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\CanBeLiked;

/**
 * Class Post
 */
class Post extends Model
{
    use CanBeLiked;

    protected $fillable = ['title'];
}