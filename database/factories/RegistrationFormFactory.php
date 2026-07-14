<?php

namespace Database\Factories;

use App\Models\League;
use App\Models\RegistrationForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistrationForm>
 */
class RegistrationFormFactory extends Factory
{
    protected $model = RegistrationForm::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'league_id' => League::factory(),
            'division_name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'type' => $this->faker->randomElement(['sports', 'other']),
        ];
    }
}
