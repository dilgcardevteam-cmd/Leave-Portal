<?php

namespace Database\Factories;

use App\Models\LeaveCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
    
class LeaveCategoryFactory extends Factory
{
    protected $model = LeaveCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true).' Leave',
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
