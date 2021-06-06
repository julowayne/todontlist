<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_empty_input()
    {
        $response = $this->postJson('/api/auth/register');
        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function test_invalid_input()
    {
        $data = [
            'email' => $this->faker->name,
            'password' => $this->faker->password,
        ];

        $response = $this->postJson('/api/auth/register', $data);
        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function test_register_with_success()
    {
        $password = $this->faker->password(8);

        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $form = [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $password
        ];


        $response = $this->postJson('/api/auth/register', $form);

        $this->assertDatabaseHas('users', $user);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'name', 'email', 'created_at'])
            ->assertJson(['email' => $user['email'], 'name' => $user['name']]);
    }


    public function test_already_registered()
    {

        $form = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8)
        ];

        $this->postJson('/api/auth/register', $form);

        $response = $this->postJson('/api/auth/register', $form);

        $response->assertStatus(409)->assertJsonStructure(['error']);
    }
}
