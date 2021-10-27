<?php

namespace Tests\Feature;

use App\Models\Episode;
use App\Models\Show;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAreaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка вызова метода обновления пользователя не аутентифицированным пользователем.
     */
    public function testUpdateUserByGuest()
    {
        $response = $this->patchJson(route('user.update'), []);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
    }

    /**
     * Проверка вызова метода обновления пользователя с пустыми параметрами.
     */
    public function testValidationForUpdateUserRoute()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), []);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name', 'email']]);
        $response->assertJsonFragment([
            'name' => ['Поле Имя обязательно для заполнения.'],
            'email' => ['Поле E-Mail адрес обязательно для заполнения.']
        ]);
    }

    /**
     * Проверка вызова метода обновления пользователя с уже занятым email.
     * Ожидается ошибка сообщающая о занятости переданного адреса.
     */
    public function testEmailUniqueValidationForUpdateUserRoute()
    {
        $other = User::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), ['email' => $other->email]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email' => ['Такое значение поля E-Mail адрес уже существует.']
        ]);
    }

    /**
     * Проверка вызова метода обновления пользователя без изменения email.
     * Ожидается что запрос будет выполнен успешно.
     * (текущий email адрес пользователя не учитывается при проверке существующих адресов)
     */
    public function testByPassEmailValidationForUpdateUserRoute()
    {
        $user = User::factory()->create();
        $new = User::factory()->make();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), ['email' => $user->email, 'name' => $new->name]);

        $response->assertStatus(200);
    }

    /**
     * Проверка вызова метода обновления профиля с изменением email адреса и загрузкой аватара.
     */
    public function testUpdateUser()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $newUser = User::factory()->make();
        $file = UploadedFile::fake()->image('photo1.jpg');

        $data = [
            'email' => $newUser->email,
            'name' => $newUser->name,
            'password' => $newUser->password,
            'password_confirmation' => $newUser->password,
            'file' => $file,
        ];

        $response = $this->patchJson(route('user.update'), $data);

        $response->assertJsonFragment([
            'name' => $newUser->name,
            'email' => $newUser->email,
            'avatar' => $file->hashName(),
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => $newUser->name,
            'email' => $newUser->email,
            'avatar' => $file->hashName(),
        ]);
    }

    /**
     * Запрос на роут закрытый авторизацией без использования токена должен вернуть ошибку авторизации
     */
    public function testCallUserAreaRouteShouldReturnErrorWithoutToken()
    {
        $response = $this->getJson(route('user.shows.index'));

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
    }

    /**
     * Запрос на роут с токеном пользователя должен вернуть список сериалов отслеживаемых пользователем
     */
    public function testCallUserAreaRouteShouldReturnDataWithToken()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson(route('user.shows.index'));

        $response->assertStatus(200);
    }

    /**
     * Проверка возвращения сериалов просматриваемых пользователем.
     */
    public function testGetUserShows()
    {
        $other = Show::factory()->count(2)->create();
        $user = User::factory()->has(Show::factory()->count(3)->has(Episode::factory()->count(5)))->create();
        $user->episodes()->attach($user->shows()->first()->episodes()->take(3)->pluck('id'));
        $user->episodes()->attach($user->shows()->latest('id')->first()->episodes()->pluck('id'));

        Sanctum::actingAs($user);

        $response = $this->getJson(route('user.shows.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure(['data' => [['title', 'watch_status', 'watched_episodes']]]);
        $response->assertSee($user->shows()->first()->title);
        $response->assertDontSee($other->first()->title);
    }

    /**
     * Проверка получения не просмотренных эпизодов.
     * Привязываем 5 эпизодов к просматриваемому сериалу,
     * отмечаем просмотренными 3 из них.
     */
    public function testGetUserNotWatchedEpisodes()
    {
        $user = User::factory()->has(Show::factory()->count(3)->has(Episode::factory()->count(5)))->create();
        $show = $user->shows()->first();
        $user->episodes()->attach($show->episodes()->take(3)->pluck('id'));

        Sanctum::actingAs($user);

        $response = $this->getJson(route('user.shows.new-episodes', $show));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure(['data' => [['title', 'show_id', 'season', 'episode_number']]]);
        $response->assertJsonFragment(['show_id' => $show->id]);
    }

    /**
     * Проверка добавления сериала в список просматриваемых.
     */
    public function testAddShowToWatchList()
    {
        Sanctum::actingAs(User::factory()->create());

        $show = Show::factory()->create();

        $response = $this->postJson(route('user.shows.watch', $show));

        $response->assertStatus(201);

        $this->assertEquals($show->id, User::first()->shows->first()->id);
    }

    /**
     * Проверка удаления сериала из списка просматриваемых.
     */
    public function testRemoveShowFromWatchList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $user->shows()->attach($show);

        $response = $this->deleteJson(route('user.shows.unwatch', $show));

        $response->assertStatus(201);

        $this->assertEmpty(User::first()->shows);
    }

    /**
     * Проверка удаления не просматриваемого сериала из списка просматриваемых.
     */
    public function testRemoveNotWatchedShowFromWatchList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();

        $response = $this->deleteJson(route('user.shows.unwatch', $show));

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Сериал не входит список просматриваемых пользователем.']);
    }

    /**
     * Проверка добавления эпизода в список просматриваемых пользователем.
     */
    public function testAddEpisodeToWatchedList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $user->shows()->attach($show);
        $episode = Episode::factory()->for($show)->create();

        $response = $this->postJson(route('user.episodes.watch', $episode));

        $response->assertStatus(201);

        $this->assertEquals($episode->show_id, User::first()->shows->first()->id);
        $this->assertEquals($episode->id, User::first()->episodes->first()->id);
    }

    /**
     * Проверка добавления эпизода не просматриваемого сериала в список просматриваемых пользователем.
     */
    public function testAddEpisodeOfNotWatchedShowToWatchedList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $episode = Episode::factory()->for($show)->create();

        $response = $this->postJson(route('user.episodes.watch', $episode));

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Сериал не входит список просматриваемых пользователем.']);
    }

    /**
     * Проверка удаления эпизода из списка просмотренных.
     */
    public function testRemoveEpisodeFromWatchedList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $user->shows()->attach($show);
        $episode = Episode::factory()->for($show)->create();
        $user->episodes()->attach($episode);

        $response = $this->deleteJson(route('user.episodes.unwatch', $episode));

        $response->assertStatus(201);

        $this->assertEmpty(User::first()->episodes);
    }

    /**
     * Проверка попытки удаления не просмотренного эпизода из списка просмотренных.
     */
    public function testRemoveNotWatchedEpisodeFromWatchedList()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $user->shows()->attach($show);
        $episode = Episode::factory()->for($show)->create();

        $response = $this->deleteJson(route('user.episodes.unwatch', $episode));

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Эпизод не входит список просматриваемых пользователем.']);
    }

    /**
     * Проверка добавления оценки сериалу.
     */
    public function testVoteForShow()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $user->shows()->attach($show);

        $response = $this->postJson(route('user.shows.vote', $show), ['vote' => random_int(1,5)]);

        $response->assertStatus(201);
    }

    /**
     * Проверка добавления оценки за пределами допустимого значения сериалу.
     */
    public function testValidateVoteForShow()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $show = Show::factory()->create();
        $user->shows()->attach($show);

        $response = $this->postJson(route('user.shows.vote', $show), ['vote' => random_int(6,10)]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['vote']]);
        $response->assertJsonFragment(['vote' => ['Поле vote должно быть между 1 и 5.']]);
    }
}
