<?php


namespace Tests;

use Overtrue\LaravelLike\Like;

/**
 * Class FeatureTest
 */
class FeatureTest extends TestCase
{
    public function testBasicFeatures()
    {
        config(['like.user_model' => User::class]);

        $user = User::create(['name' => 'overtrue']);
        $post = Post::create(['title' => 'Hello world!']);

        $user->like($post);

        $user->refresh();
        $post->refresh();

        $this->assertTrue($user->hasLiked($post));
        $this->assertTrue($post->isLikedBy($user));
    }
}