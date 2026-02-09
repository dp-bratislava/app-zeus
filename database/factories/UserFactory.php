<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'login' => $this->faker->name(),
            'firstname' => $this->faker->name(),
            'lastname' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // default password
            'remember_token' => Str::random(10),
            'first_login' => 0,
        ];
    }
}
