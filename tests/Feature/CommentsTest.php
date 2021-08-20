<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    public function testEpisodeComments()
    {
        $count = random_int(2, 10);

        $episode = Episode::factory()->has(
            Comment::factory()->count($count)->for(User::factory()->create())
        )->for(Show::factory())->create();
        $comment = $episode->comments->first();

        $response = $this->getJson(route('comments.index', $episode->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['count' => $count]);
        $response->assertJsonCount($count, 'comments');
        $response->assertJsonFragment([
            'id' => $comment->id,
            'comment' => $comment->comment,
            'parent_id' => $comment->parent_id,
            'created_at' => $comment->created_at,
            'user' => [
                'name' => $comment->user->name,
                'avatar' => $comment->user->avatar,
            ],
        ]);
    }
}