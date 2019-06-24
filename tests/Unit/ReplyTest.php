<?php

namespace Tests\Unit;

use App\Reply;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_has_an_owner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test */
    function knows_if_it_was_just_published()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    function detect_all_mentioned_users_in_the_body()
    {
        $reply = new Reply ([
            'body' => '@Pece wants to talk with @marko'
        ]);

        $this->assertEquals(['Pece', 'marko'], $reply->mentionedUsers());

    }

    /** @test */
    function wraps_mentioned_users_within_anchor_tag()
    {
        $reply = new Reply([
            'body' => 'Hello @Pece.'
        ]);
        $this->assertEquals(
            'Hello <a href="/profiles/Pece">@Pece</a>.',
            $reply->body
        );
    }

    /** @test */
    function it_knows_if_it_is_best_reply()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());
    }
}
