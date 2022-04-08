<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected const registerRouteName = 'register.index';

    public function test_should_have_status_400_when_validation_requirement_not_satisfied()
    {
        $response = $this->post(route(self::registerRouteName), [
            'name' => '',
            'username' => '',
            'password' => ''
        ]);

        $response->assertStatus(400);
    }

    public function test_should_give_error_message_about_username_exists_when_user_give_username_that_already_been_taken()
    {
        User::factory()->state([
            'username' => 'johndoe69420'
        ])->create();

        $response = $this->post(route(self::registerRouteName), [
            'name' => 'John Doe',
            'username' => 'johndoe69420',
            'password' => 'johndoe12341234'
        ]);

        $response
            ->assertStatus(400)
            ->assertJsonPath(
                'username.0',
                'The username has already been taken.'
            );
    }

    protected function registerValidationSuccessResponse()
    {
        return $this->post(route(self::registerRouteName), [
            'name' => 'Muhammad Azhari',
            'username' => 'azharnotanymore',
            'password' => 'pokemondragoon123'
        ]);
    }

    public function test_should_persist_new_user_to_database_when_validation_is_success()
    {
        $this->registerValidationSuccessResponse();

        $this->assertDatabaseHas('users', [
            'username' => 'azharnotanymore'
        ]);
    }

    public function test_should_create_newly_token_for_user_when_validation_is_success()
    {
        $this->registerValidationSuccessResponse();

        $registered = User::latest()->first();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $registered->id
        ]);
    }

    public function test_should_have_status_200_when_validation_is_success()
    {
        $response = $this->registerValidationSuccessResponse();

        $response->assertStatus(200);
    }

    public function test_should_response_with_token_when_validation_is_success()
    {
        $response = $this->registerValidationSuccessResponse();

        $response->assertJsonStructure([
            'data' => [
                'token'
            ]
        ]);
    }
}
