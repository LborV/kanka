<?php

namespace Database\Factories;

use App\Models\CalendarTimeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarTimeEntryFactory extends Factory
{
    protected $model = CalendarTimeEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day' => $this->faker->numberBetween(1, 28),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => $this->faker->numberBetween(1, 100),
            'start_hour' => $this->faker->numberBetween(0, 23),
            'start_minute' => $this->faker->randomElement([0, 15, 30, 45]),
            'duration' => $this->faker->randomElement([15, 30, 45, 60, 90, 120]),
            'name' => $this->faker->sentence(3),
        ];
    }
}
