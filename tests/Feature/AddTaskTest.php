<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class AddTaskTest extends TestCase
{
    public function test_no_input()
    {
        $credentials = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8)
        ];
        $user = User::create($credentials);
        $this->actingAs($user);

        $response = $this->postJson('/api/tasks');
        $response->assertStatus(422);
    }

    public function test_invalid_input()
    {
        $credentials = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8)
        ];
        $user = User::create($credentials);
        $this->actingAs($user);

        $task = [
            'body' => ''
        ];

        $response = $this->postJson('/api/tasks', $task);
        $response->assertStatus(422);
    }

    public function test_add_task_with_success()
    {
        $credentials = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8)
        ];
        $user = User::create($credentials);
        $this->actingAs($user);

        
        $taskData = [
            'body' => $this->faker->text(),
            'done' => true,
            'user_id' => 1
        ]; 

        $task = Task::create($taskData);
        $response = $this->postJson('/api/tasks', $taskData);
        $this->assertDatabaseHas('tasks', $taskData);
        $response->assertStatus(201);
    }
}
