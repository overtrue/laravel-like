<h1 align="center"> Laravel Like </h1>

<p align="center"> 👍 User-like features for Laravel Application.</p>


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

#### `Overtrue\LaravelLike\Traits\CanLike`

```php

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Overtrue\LaravelLike\Traits\CanLike;

class User extends Authenticatable
{
    use Notifiable, CanLike;
    
    <...>
}
```

#### `Overtrue\LaravelLike\Traits\CanBeLiked`

```php
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\CanBeLiked;

class Post extends Model
{
    use CanBeLiked;

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

Get User liked items:

```php
$items = $user->likedItems(); 

foreach ($items as $item) {
    // 
}
```

Get object likers:

```php
foreach($post->likers as $user) {
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
// CanLike
$users = App\User::with('likes')->get();

foreach($users as $user) {
    $user->hasLiked($post);
}

// CanBeLiked
$posts = App\Post::with('likes')->get();
// or 
$posts = App\Post::with('likers')->get();

foreach($posts as $post) {
    $post->isLikedBy($user);
}
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/laravel-likes/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/laravel-likes/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
