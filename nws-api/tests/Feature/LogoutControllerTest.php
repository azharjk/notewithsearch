<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    protected const logoutRouteName = 'logout.index';

    protected function register()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $token;
    }

    public function test_should_delete_all_tokens_that_user_had()
    {
        $token = $this->register();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => User::latest()->first()->id
        ]);

        $this->post(route(self::logoutRouteName), [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => User::latest()->first()->id
        ]);
    }

    protected function logoutResponse()
    {
        $token = $this->register();

        return $this->post(route(self::logoutRouteName), [], [
            'Authorization' => 'Bearer ' . $token
        ]);
    }

    public function test_should_have_message_logged_out()
    {
        $response = $this->logoutResponse();

        $response->assertJsonPath('message', 'Logged out');
    }

    public function test_should_have_status_200()
    {
        $response = $this->logoutResponse();

        $response->assertStatus(200);
    }
}
