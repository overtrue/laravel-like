Laravel Like 
--- 

👍 User-like features for Laravel Application.

![CI](https://github.com/overtrue/laravel-like/workflows/CI/badge.svg)

## Installing

```shell
$ composer require overtrue/laravel-like -vvv
```

### Configuration

This step is optional

```php
$ php artisan vendor:publish --provider="Overtrue\\LaravelLike\\LikeServiceProvider" --tag=config
```

### Migrations

This step is also optional, if you want to custom likes table, you can publish the migration files:

```php
$ php artisan vendor:publish --provider="Overtrue\\LaravelLike\\LikeServiceProvider" --tag=migrations
```


## Usage

### Traits

#### `Overtrue\LaravelLike\Traits\Liker`

```php

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Overtrue\LaravelLike\Traits\Liker;

class User extends Authenticatable
{
    use Liker;
    
    <...>
}
```

#### `Overtrue\LaravelLike\Traits\Likeable`

```php
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;

class Post extends Model
{
    use Likeable;

    <...>
}
```

### API

```php
$user = User::find(1);
$post = Post::find(2);

$user->like($post);
$user->unlike($post);
$user->toggleLike($post);

$user->hasLiked($post); 
$post->isLikedBy($user); 
```

Get user likes with pagination:

```php
$likes = $user->likes()->with('likeable')->paginate(20);

foreach ($likes as $like) {
    $like->likeable; // App\Post instance
}
```

Get object likers:

```php
foreach($post->likers as $user) {
    // echo $user->name;
}
```

with pagination:

```php
$likers = $post->likers()->paginate(20);

foreach($likers as $user) {
    // echo $user->name;
}
```

### Aggregations

```php
// all
$user->likes()->count(); 

// with type
$user->likes()->withType(Post::class)->count(); 

// likers count
$post->likers()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount('likes')->get();

foreach($users as $user) {
    echo $user->likes_count;
}
```

### N+1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the `with` method:

```php
// Liker
$users = App\User::with('likes')->get();

foreach($users as $user) {
    $user->hasLiked($post);
}

// Likeable
$posts = App\Post::with('likes')->get();
// or 
$posts = App\Post::with('likers')->get();

foreach($posts as $post) {
    $post->isLikedBy($user);
}
```


### Events

| **Event** | **Description** |
| --- | --- |
|  `Overtrue\LaravelLike\Events\Liked` | Triggered when the relationship is created. |
|  `Overtrue\LaravelLike\Events\Unliked` | Triggered when the relationship is deleted. |

## Related packages

- Follow: [overtrue/laravel-follow](https://github.com/overtrue/laravel-follow)
- Like: [overtrue/laravel-like](https://github.com/overtrue/laravel-like)
- Favorite: [overtrue/laravel-favorite](https://github.com/overtrue/laravel-favorite)
- Subscribe: [overtrue/laravel-subscribe](https://github.com/overtrue/laravel-subscribe)
- Vote: overtrue/laravel-vote (working in progress)
- Bookmark: overtrue/laravel-bookmark (working in progress)


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/laravel-likes/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/laravel-likes/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
