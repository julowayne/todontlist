<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_empty_input()
    {
        $response = $this->postJson('/api/auth/login');
        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }
    public function test_invalid_input()
    {
        $data = [
            'email' => $this->faker->name(),
            'password' => $this->faker->password(),
        ];

        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }
    public function test_invalid_credentials()
    {
        $data = [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];

        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(401)->assertJsonStructure(['errors']);
    }
    public function test_login_with_success()
    {
        $password = $this->faker->password(8);

        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($password),
        ];

        $user = User::create($userData);

        $formData = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson('/api/auth/login', $formData);

        $this->assertDatabaseHas('users', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'name', 'email'])
            ->assertJson(['email' => $user->email]);
    }
}
