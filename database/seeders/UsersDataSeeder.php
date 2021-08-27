<?php

namespace Database\Seeders;

use App\Models\Show;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        /** @var User $user */
        $user = User::factory()
            ->create([
                'email' => 'demo@laravel.localhost',
                'password' => Hash::make('12345678'),
            ]);

        $user->shows()->attach(Show::inRandomOrder()->limit(3)->pluck('id'));
    }
}
