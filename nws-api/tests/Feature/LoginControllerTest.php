<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Hash;

use App\Models\User;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected const loginRouteName = 'login.index';

    protected function loginInvalidResponse()
    {
        return $this->post(route(self::loginRouteName), [
            'username' => '',
            'password' => ''
        ]);
    }

    public function test_should_have_status_400_when_validation_requirement_not_satisfied()
    {
        $response = $this->loginInvalidResponse();

        $response->assertStatus(400);
    }

    public function test_should_give_required_message_when_validation_fail()
    {
        $response = $this->loginInvalidResponse();

        $response->assertJsonPath(
            'username.0',
            'The username field is required.'
        )->assertJsonPath(
            'password.0',
            'The password field is required.'
        );
    }

    protected function loginUsernameIncorrectResponse()
    {
        return $this->post(route(self::loginRouteName), [
            'username' => 'johndoe12341234',
            'password' => 'abcdefghijklmnop1234'
        ]);
    }

    public function test_should_have_status_401_when_username_is_incorrect()
    {
        $response = $this->loginUsernameIncorrectResponse();

        $response->assertStatus(401);
    }

    public function test_should_give_error_message_when_username_is_incorrect()
    {
        $response = $this->loginUsernameIncorrectResponse();

        $response->assertJson([
            'message' => 'Username is incorrect'
        ]);
    }

    protected function createUser()
    {
        User::factory()->state([
            'username' => 'facialguy1234',
            'password' => Hash::make('guywithgooddog1234')
        ])->create();
    }

    protected function loginPasswordIncorrectResponse()
    {
        $this->createUser();

        return $this->post(route(self::loginRouteName), [
            'username' => 'facialguy1234',
            'password' => 'abcdefghijklmnop1234'
        ]);
    }

    public function test_should_have_status_401_when_password_is_incorrect()
    {
        $response = $this->loginPasswordIncorrectResponse();

        $response->assertStatus(401);
    }

    public function test_should_give_error_message_when_password_is_incorrect()
    {
        $response = $this->loginPasswordIncorrectResponse();

        $response->assertJson([
            'message' => 'Password is incorrect'
        ]);
    }

    protected function loginValidResponse()
    {
        $this->createUser();

        return $this->post(route(self::loginRouteName), [
            'username' => 'facialguy1234',
            'password' => 'guywithgooddog1234'
        ]);
    }

    public function test_should_create_newly_token_for_user_when_validation_and_cred_check_is_success()
    {
        $this->loginValidResponse();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => User::latest()->first()->id
        ]);
    }

    public function test_should_response_with_token_when_validation_and_cred_check_is_success()
    {
        $response = $this->loginValidResponse();

        $response->assertJsonStructure([
            'data' => [
                'token'
            ]
        ]);
    }
}
