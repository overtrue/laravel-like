<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Overtrue\LaravelLike\Events\Liked;
use Overtrue\LaravelLike\Events\Unliked;

class FeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        config(['auth.providers.users.model' => User::class]);
    }

    public function test_basic_features()
    {
        $user = User::create(['name' => 'overtrue']);
        $post = Post::create(['title' => 'Hello world!']);

        $user->like($post);

        Event::assertDispatched(Liked::class, function ($event) use ($user, $post) {
            return $event->like->likeable instanceof Post
                && $event->like->user instanceof User
                && $event->like->user->id === $user->id
                && $event->like->likeable->id === $post->id;
        });

        $this->assertTrue($user->hasLiked($post));
        $this->assertTrue($post->isLikedBy($user));

        $this->assertTrue($user->unlike($post));

        Event::assertDispatched(Unliked::class, function ($event) use ($user, $post) {
            return $event->like->likeable instanceof Post
                && $event->like->user instanceof User
                && $event->like->user->id === $user->id
                && $event->like->likeable->id === $post->id;
        });
    }

    public function test_unlike_features()
    {
        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'allen']);
        $user3 = User::create(['name' => 'taylor']);

        $post = Post::create(['title' => 'Hello world!']);

        $user2->like($post);
        $user3->like($post);
        $user1->like($post);

        $user1->unlike($post);

        $this->assertFalse($user1->hasLiked($post));
        $this->assertTrue($user2->hasLiked($post));
        $this->assertTrue($user3->hasLiked($post));
    }

    public function test_aggregations()
    {
        $user = User::create(['name' => 'overtrue']);

        $post1 = Post::create(['title' => 'Hello world!']);
        $post2 = Post::create(['title' => 'Hello everyone!']);
        $book1 = Book::create(['title' => 'Learn laravel.']);
        $book2 = Book::create(['title' => 'Learn symfony.']);

        $user->like($post1);
        $user->like($post2);
        $user->like($book1);
        $user->like($book2);

        $this->assertSame(4, $user->likes()->count());
        $this->assertSame(4, $user->totalLikes);
        $this->assertSame(2, $user->likes()->withType(Book::class)->count());
    }

    public function test_like_same_model()
    {
        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'allen']);
        $user3 = User::create(['name' => 'taylor']);

        $user1->like($user2);

        $this->assertTrue($user1->hasLiked($user2));
        $this->assertTrue($user2->isLikedBy($user1));
    }

    public function test_object_likers()
    {
        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'allen']);
        $user3 = User::create(['name' => 'taylor']);

        $post = Post::create(['title' => 'Hello world!']);

        $user1->like($post);
        $user2->like($post);

        $this->assertCount(2, $post->likers);
        $this->assertSame('overtrue', $post->likers[0]['name']);
        $this->assertSame('allen', $post->likers[1]['name']);

        $sqls = $this->getQueryLog(function () use ($post, $user1, $user2, $user3) {
            $this->assertTrue($post->isLikedBy($user1));
            $this->assertTrue($post->isLikedBy($user2));
            $this->assertFalse($post->isLikedBy($user3));
        });

        $this->assertEmpty($sqls->all());
    }

    public function test_object_likers_with_custom_morph_class_name()
    {
        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'allen']);
        $user3 = User::create(['name' => 'taylor']);

        $post = Post::create(['title' => 'Hello world!']);

        Relation::morphMap([
            'posts' => Post::class,
        ]);

        $user1->like($post);
        $user2->like($post);

        $this->assertCount(2, $post->likers);
        $this->assertSame('overtrue', $post->likers[0]['name']);
        $this->assertSame('allen', $post->likers[1]['name']);
    }

    public function test_eager_loading()
    {
        $user = User::create(['name' => 'overtrue']);

        $post1 = Post::create(['title' => 'Hello world!']);
        $post2 = Post::create(['title' => 'Hello everyone!']);
        $book1 = Book::create(['title' => 'Learn laravel.']);
        $book2 = Book::create(['title' => 'Learn symfony.']);

        $user->like($post1);
        $user->like($post2);
        $user->like($book1);
        $user->like($book2);

        // start recording
        $sqls = $this->getQueryLog(function () use ($user) {
            $user->load('likes.likeable');
        });

        $this->assertSame(3, $sqls->count());

        // from loaded relations
        $sqls = $this->getQueryLog(function () use ($user, $post1) {
            $user->hasLiked($post1);
        });

        $this->assertEmpty($sqls->all());
    }

    public function test_liker_can_attach_like_status_to_votable_collection()
    {
        /* @var \Tests\Post $post1 */
        $post1 = Post::create(['title' => 'Post title1']);
        /* @var \Tests\Post $post2 */
        $post2 = Post::create(['title' => 'Post title2']);
        /* @var \Tests\Post $post3 */
        $post3 = Post::create(['title' => 'Post title3']);

        /* @var \Tests\User $user */
        $user = User::create(['name' => 'overtrue']);

        $user->like($post1);
        $user->like($post2);

        // model
        $post1 = Post::find($post1->id);
        $this->assertNull($post1->has_liked);
        $user->attachLikeStatus($post1);
        $this->assertTrue($post1->has_liked);

        // collection
        $posts = Post::oldest('id')->get();
        $user->attachLikeStatus($posts);
        $this->assertTrue($posts[0]['has_liked']);
        $this->assertTrue($posts[1]['has_liked']);
        $this->assertFalse($posts[2]['has_liked']);

        // lazy collection
        $posts = Post::oldest('id')->cursor();
        $user->attachLikeStatus($posts);
        $posts = $posts->toArray();
        $this->assertTrue($posts[0]['has_liked']);
        $this->assertTrue($posts[1]['has_liked']);
        $this->assertFalse($posts[2]['has_liked']);

        // paginator
        $posts = Post::oldest('id')->paginate();
        $user->attachLikeStatus($posts);
        $this->assertTrue($posts[0]['has_liked']);
        $this->assertTrue($posts[1]['has_liked']);
        $this->assertFalse($posts[2]['has_liked']);

        // cursor paginator
        $posts = Post::oldest('id')->cursorPaginate();
        $user->attachLikeStatus($posts);
        $this->assertTrue($posts[0]['has_liked']);
        $this->assertTrue($posts[1]['has_liked']);
        $this->assertFalse($posts[2]['has_liked']);

        // array
        $posts = Post::oldest('id')->get()->all();
        $user->attachLikeStatus($posts);
        $this->assertTrue($posts[0]['has_liked']);
        $this->assertTrue($posts[1]['has_liked']);
        $this->assertFalse($posts[2]['has_liked']);

        // custom resolver
        $posts = [['post' => $post1], ['post' => $post2], ['post' => $post3]];
        $user->attachLikeStatus($posts, fn ($i) => $i['post']);

        $this->assertTrue($posts[0]['post']['has_liked']);
        $this->assertTrue($posts[1]['post']['has_liked']);
        $this->assertFalse($posts[2]['post']['has_liked']);
    }

    protected function getQueryLog(\Closure $callback): \Illuminate\Support\Collection
    {
        $sqls = \collect([]);
        \DB::listen(function ($query) use ($sqls) {
            $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
        });

        $callback();

        return $sqls;
    }
}
