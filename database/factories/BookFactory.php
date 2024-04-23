<?php

namespace Database\Factories;

use App\Models\Book; // Replace with your Book model path
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * Define the model for the factory.
     *
     * @return string
     */
    public function model()
    {
        return Book::class;
    }

    /**
     * Define the states for the factory.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'author' => $this->faker->name,
        ];
    }
}