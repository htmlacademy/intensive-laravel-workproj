<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
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
        $response->assertJsonCount($count, 'data.comments');
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

    public function testAddEpisodeComment()
    {
        Sanctum::actingAs(User::factory()->create());
        $episode = Episode::factory()->has(
            Comment::factory()->for(User::factory()->create())
        )->for(Show::factory())->create();

        $newComment = Comment::factory()->make();

        $response = $this->postJson(route('comments.store', [
            'episode' => $episode,
            'comment' => $episode->comments()->first()
        ]), ['text' => $newComment->comment]);

        $response->assertStatus(201);
    }

    /**
     * Ожидается получение ошибки валидации,
     * в случае отправки запроса на добавления комментария, без отправки текста.
     */
    public function testValidationErrorFOrAddEpisodeCommentRoute()
    {
        Sanctum::actingAs(User::factory()->create());
        $episode = Episode::factory()->has(
            Comment::factory()->for(User::factory()->create())
        )->for(Show::factory())->create();

        $response = $this->postJson(route('comments.store', [
            'episode' => $episode,
            'comment' => $episode->comments()->first()
        ]));

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['text']]);
    }

    /**
     * Ожидается получение ошибки аутентификации,
     * если запрос на добавление комментария выполняется не аутентифицированным пользователем.
     */
    public function testAuthErrorFOrAddEpisodeCommentRoute()
    {
        $episode = Episode::factory()->has(
            Comment::factory()->for(User::factory()->create())
        )->for(Show::factory())->create();

        $newComment = Comment::factory()->make();

        $response = $this->postJson(route('comments.store', [
            'episode' => $episode,
            'comment' => $episode->comments()->first()
        ]), ['text' => $newComment->comment]);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
    }

    public function testDeleteComment()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());
        $episode = Episode::factory()->for(Show::factory())->create();
        $comment = Comment::factory()->for(User::factory()->create())->for($episode)->create();

        $response = $this->deleteJson(route('comments.destroy', $comment->id));

        $response->assertStatus(201);
    }

    public function testDeleteCommentByGuest()
    {
        $episode = Episode::factory()->for(Show::factory())->create();
        $comment = Comment::factory()->for(User::factory()->create())->for($episode)->create();

        $response = $this->deleteJson(route('comments.destroy', $comment->id));

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
    }

    public function testDeleteCommentByCommonUser()
    {
        Sanctum::actingAs(User::factory()->create());
        $episode = Episode::factory()->for(Show::factory())->create();
        $comment = Comment::factory()->for(User::factory()->create())->for($episode)->create();

        $response = $this->deleteJson(route('comments.destroy', $comment->id));

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Неавторизованное действие.']);
    }
}
