<?php

namespace Tests\Unit;

use App\Models\Episode;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка вычисления значения total_seasons.
     */
    public function testGetTotalSeasons()
    {
        $show = Show::factory()->create();

        Episode::factory()->for($show)->create(['season' => 1]);
        Episode::factory()->for($show)->create(['season' => 2]);
        Episode::factory()->for($show)->create(['season' => 2]);

        $this->assertEquals(2, $show->total_seasons);
    }

    /**
     * Проверка вычисления значения total_episodes.
     */
    public function testGetTotalEpisodes()
    {
        $count = random_int(2, 4);
        Show::factory()->has(Episode::factory()->count($count))->create();

        $this->assertEquals($count, Show::first()->total_episodes);
    }

    /**
     * Проверка вычисления значения rating, пользовательской оценки сериала.
     * Ожидается среднее значение округленное по правилам математики.
     */
    public function testGetRating()
    {
        $show = Show::factory()->create();
        $show->users()->attach(User::factory()->create(), ['vote' => 1]);
        $show->users()->attach(User::factory()->create(), ['vote' => 5]);
        $show->users()->attach(User::factory()->create(), ['vote' => 5]);

        $this->assertEquals(4, Show::first()->rating);
    }

    /**
     * Проверка получения оценки пользователя.
     */
    public function testGetUserVote()
    {
        $value = random_int(1, 5);
        $user = User::factory()->create();
        $show = Show::factory()->create();
        $show->users()->attach($user, ['vote' => $value]);

        $this->actingAs($user);

        $this->assertEquals($value, Show::first()->user_vote);
    }

    /**
     * Проверка получения пустой оценки (null) для не аутентифицированного пользователя.
     * Оценки поставленные другими пользователями не должны влиять на результат.
     */
    public function testGetUserVoteForGuest()
    {
        $show = Show::factory()->create();
        $show->users()->attach(User::factory()->create(), ['vote' => 1]);

        $this->assertNull(Show::first()->user_vote);
    }

    /**
     * Проверка получения значения 0 в поле к-во просмотренных серий, для не аутентифицированного пользователя.
     * Просмотры отмеченные другими пользователями не должны влиять на результат.
     */
    public function testGetWatchedEpisodesForGuest()
    {
        $show = Show::factory()->create();
        Episode::factory()->for($show)->hasAttached(User::factory()->create())->create();

        $this->assertEquals(0, Show::first()->watched_episodes);
    }

    /**
     * Проверка получения к-ва просмотренных серий для пользователя.
     * Просмотры отмеченные другими пользователями не должны влиять на результат.
     */
    public function testGetWatchedEpisodesForUser()
    {
        $user = User::factory()->create();
        $show = Show::factory()->create();
        Episode::factory()->for($show)->hasAttached($user)->create();
        Episode::factory()->for($show)->hasAttached($user)->create();
        Episode::factory()->for($show)->hasAttached(User::factory()->create())->create();

        $this->actingAs($user);

        $this->assertEquals(2, Show::first()->watched_episodes);
    }

    /**
     * Получение пустого статуса просмотра сериала, для не аутентифицированного пользователя.
     */
    public function testGetWatchedStatusForGuest()
    {
        $show = Show::factory()->create();
        Episode::factory()->for($show)->create();

        $this->assertNull(Show::first()->watch_status);
    }

    /**
     * Получение пустого статуса просмотра сериала, для сериала не отмеченного пользователем.
     */
    public function testGetEmptyStatusForUser()
    {
        $show = Show::factory()->create();
        Episode::factory()->for($show)->create();

        $this->actingAs(User::factory()->create());

        $this->assertNull(Show::first()->watch_status);
    }

    /**
     * Получение статуса просмотра сериала пользователем.
     * Ожидается статус "watching" если не все серии отмечены как просмотренные,
     * и сериал находится в просматриваемых.
     */
    public function testGetWatchingStatusForUser()
    {
        $user = User::factory()->create();
        $show = Show::factory()->hasAttached($user)->create();
        Episode::factory()->for($show)->hasAttached($user)->create();
        Episode::factory()->for($show)->create();

        $this->actingAs($user);

        $this->assertEquals(Show::USER_WATCHING_STATUS, Show::first()->watch_status);
    }

    /**
     * Получение статуса просмотра сериала пользователем.
     * Ожидается статус "watched" если не все серии отмечены как просмотренные.
     */
    public function testGetWatchCompletedStatusForUser()
    {
        $user = User::factory()->create();
        $show = Show::factory()->hasAttached($user)->create();
        Episode::factory()->for($show)->hasAttached($user)->create();
        Episode::factory()->for($show)->hasAttached($user)->create();

        $this->actingAs($user);

        $this->assertEquals(Show::USER_WATCHED_STATUS, Show::first()->watch_status);
    }
}
