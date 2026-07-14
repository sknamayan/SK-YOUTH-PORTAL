<?php

namespace Database\Factories;

use App\Models\RegistrationForm;
use App\Models\RegistrationResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistrationResponse>
 */
class RegistrationResponseFactory extends Factory
{
    protected $model = RegistrationResponse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registration_form_id' => RegistrationForm::factory(),
            'citizen_name' => $this->faker->name(),
            'citizen_email' => $this->faker->unique()->safeEmail(),
            'answers' => [],
            'status' => 'Pending',
            'processed_by' => null,
        ];
    }
}
