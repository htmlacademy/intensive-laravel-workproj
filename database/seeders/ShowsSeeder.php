<?php

namespace Database\Seeders;

use App\Models\Episode;
use App\Models\Genre;
use App\Models\Show;
use Illuminate\Database\Seeder;

class ShowsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Show::factory()
            ->count(5)
            ->has(
                Episode::factory()
                      ->count(10)
                      ->hasComments(3)
            )
            ->create()
            ->each(fn($show) => $show->genres()->attach(
                Genre::inRandomOrder()->limit(3)->pluck('id')
            ));
    }
}
