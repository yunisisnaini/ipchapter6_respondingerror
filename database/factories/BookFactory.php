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
        $title = $this->faker->sentence(rand(3, 10));
        return [
            'title' => substr($title, 0, strlen($title) - 1),
            'description' => $this->faker->text,
            'author' => $this->faker->name
        ];
    }
}