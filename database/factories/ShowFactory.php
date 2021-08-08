<?php

namespace Database\Factories;

use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Show::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'title_original' => $this->faker->words(3, true),
            'description' => $this->faker->sentences(3, true),
            'year' => $this->faker->year,
            'votes' => random_int(10, 10000),
            'rating' => random_int(100, 500) / 100,
            'status' => $this->faker->randomElement(['new', 'ended']),
        ];
    }
}
