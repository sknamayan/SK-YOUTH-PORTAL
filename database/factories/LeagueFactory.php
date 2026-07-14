<?php

namespace Database\Factories;

use App\Models\League;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<League>
 */
class LeagueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<League>
     */
    protected $model = League::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'   => $this->faker->company . ' League',
            'sport'  => $this->faker->randomElement(['soccer', 'basketball', 'baseball', 'hockey']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
