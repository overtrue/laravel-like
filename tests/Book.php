<?php


namespace Tests;


use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\CanBeLiked;

/**
 * Class Book
 */
class Book extends Model
{
    use CanBeLiked;

    protected $fillable = ['title'];
}