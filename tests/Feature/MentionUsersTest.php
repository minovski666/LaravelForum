<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function mention_users_in_a_reply_are_notify()
    {
        $john = create('App\User', ['name' => 'JohnDoe']);
        $this->signIn($john);

        $jane = create('App\User', ['name' => 'JaneDoe']);

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => 'Hey @JaneDoe check this out.'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    function it_can_fetch_all_users_with_given_characters()
    {
        create('App\User', ['name' => 'pece']);
        create('App\User', ['name' => 'pece123']);
        create('App\User', ['name' => 'marko']);

        $results = $this->json('GET', '/api/users', ['name' => 'pece']);

        $this->assertCount(2, $results->json());
    }
}
