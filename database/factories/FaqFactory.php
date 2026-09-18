<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'question' => [
                'ar' => $this->faker->sentence(),
                'en' => $this->faker->sentence(),
            ],
            'answer' => [
                'ar' => $this->faker->paragraph(),
                'en' => $this->faker->paragraph(),
            ],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
