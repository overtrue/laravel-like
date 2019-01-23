<?php

/*
 * This file is part of the overtrue/laravel-like.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

/**
 * Class FeatureTest.
 */
class FeatureTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        config(['auth.providers.users.model' => User::class]);
    }

    public function testBasicFeatures()
    {
        $user = User::create(['name' => 'overtrue']);
        $post = Post::create(['title' => 'Hello world!']);

        $user->like($post);

        $this->assertTrue($user->hasLiked($post));
        $this->assertTrue($post->isLikedBy($user));
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
        $this->assertSame(2, $user->likes()->withType(Book::class)->count());
    }

    public function test_object_likers()
    {
        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'allen']);
        $user3 = User::create(['name' => 'taylor']);

        $post = Post::create(['title' => 'Hello world!']);

        $user1->like($post);
        $user2->like($post);

        // start recording
        $sqls = \collect([]);
        \DB::listen(function ($query) use ($sqls) {
            $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
        });

        $this->assertCount(2, $post->likers);
        $this->assertSame('overtrue', $post->likers[0]['name']);
        $this->assertSame('allen', $post->likers[1]['name']);

        $sqls = \collect([]);
        $this->assertTrue($post->isLikedBy($user1));
        $this->assertTrue($post->isLikedBy($user2));
        $this->assertFalse($post->isLikedBy($user3));

        $this->assertEmpty($sqls->all());
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
        $sqls = \collect([]);
        \DB::listen(function ($query) use ($sqls) {
            $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
        });

        $user->load('likes.likable');

        $this->assertSame(3, $sqls->count());
        $this->assertSame([$post1->id, $post2->id], \array_map('intval', $sqls[1]['bindings']));

        // from loaded relations
        $sqls = \collect([]);
        $user->hasLiked($post1);
        $this->assertEmpty($sqls->all());

        // eager loading liked objects
        $items = $user->likedItems();
        $this->assertCount(4, $items);
        $this->assertInstanceOf(Post::class, $items[0]);
        $this->assertInstanceOf(Post::class, $items[1]);
        $this->assertInstanceOf(Book::class, $items[2]);
        $this->assertInstanceOf(Book::class, $items[3]);

        // filter by model name
        $likedPosts = $user->likedItems(Post::class);
        $this->assertCount(2, $likedPosts);
        $this->assertInstanceOf(Post::class, $likedPosts[0]);
        $this->assertInstanceOf(Post::class, $likedPosts[1]);
    }
}
