<?php

namespace Database\Seeders;

use App\Models\Show;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersDataSeeder extends Seeder
{
    /**
     * В этом классе описываем пользователя и связанные с ним сущности,
     * для аутентификации под текущим пользователям.
     *
     * @return void
     */
    public function run()
    {
        $data = User::factory()->make();

        /** @var User $user */
        $user = User::updateOrCreate(
            ['email' => 'demo@laravel.localhost'],
            [
                'email' => 'demo@laravel.localhost',
                'password' => '12345678',
                'name' => $data->name,
            ]
        );

        $user->shows()->sync(Show::inRandomOrder()->limit(3)->pluck('id'));
    }
}
