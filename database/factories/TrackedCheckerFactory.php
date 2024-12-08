<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CheckType;
use App\Models\TrackedChecker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrackedChecker>
 */
final class TrackedCheckerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(CheckType::cases()),
        ];
    }
}
