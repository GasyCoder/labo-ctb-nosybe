<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),  // ✅ Changé de email vers username
            'type' => 'secretaire', // ✅ Ajout du type par défaut
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model should have admin type.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'admin',
        ]);
    }

    /**
     * Indicate that the model should have secretaire type.
     */
    public function secretaire(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'secretaire',
        ]);
    }

    /**
     * Indicate that the model should have technicien type.
     */
    public function technicien(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'technicien',
        ]);
    }

    /**
     * Indicate that the model should have biologiste type.
     */
    public function biologiste(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'biologiste',
        ]);
    }
}