<?php

namespace Tests\Unit;

use App\Models\Episode;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка вычисления значения comments_count.
     */
    public function testGetCommentsCount()
    {
        $value = random_int(2,4);
        User::factory()->create();
        Episode::factory()->for(Show::factory()->create())->hasComments($value)->create();

        $this->assertEquals($value, Episode::first()->comments_count);
    }

    /**
     * Проверка обращения к методу проверки статуса просмотра гостем.
     * Ожидается возвращение false.
     */
    public function testGetWatchedStatusForGuest()
    {
        $user = User::factory()->create();
        $show = Show::factory()->hasAttached($user)->create();
        Episode::factory()->for($show)->hasAttached($user)->create();

        $this->assertFalse(Episode::first()->watched);
    }

    /**
     * Проверка обращения к методу проверки статуса просмотра пользователем, не отметившим эпизод просмотренным.
     * Ожидается возвращение false.
     */
    public function testGetWatchedStatusForUserWhoNotWatchIt()
    {
        $user = User::factory()->create();
        $show = Show::factory()->hasAttached($user)->create();
        Episode::factory()->for($show)->create();

        $this->actingAs($user);

        $this->assertFalse(Episode::first()->watched);
    }

    /**
     * Проверка обращения к методу проверки статуса просмотра пользователем, отметившим эпизод просмотренным.
     * Ожидается возвращение true.
     */
    public function testGetWatchedStatusForUser()
    {
        $user = User::factory()->create();
        $show = Show::factory()->hasAttached($user)->create();
        Episode::factory()->for($show)->hasAttached($user)->create();

        $this->actingAs($user);

        $this->assertTrue(Episode::first()->watched);
    }
}
