<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;

use App\Models\User;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    protected const noteStoreRouteName = 'note.store';

    public function test_should_have_status_400_when_title_is_empty()
    {
        $response = $this->post(route(self::noteStoreRouteName), [
            'title' => ''
        ]);

        $response->assertStatus(400);
    }

    public function test_should_have_status_400_when_title_is_more_than_60()
    {
        $response = $this->post(route(self::noteStoreRouteName), [
            'title' => Str::random(61)
        ]);

        $response->assertStatus(400);
    }

    protected function noteStoreResponse()
    {
        return $this->post(route(self::noteStoreRouteName), [
            'title' => 'AnjayMabar'
        ]);
    }

    public function test_should_persist_new_note_to_database_when_validation_success()
    {
        $this->noteStoreResponse();

        $this->assertDatabaseHas('notes', [
            'user_id' => User::latest()->first()->id,
            'title' => 'AnjayMabar'
        ]);
    }

    public function test_should_have_status_201_when_validation_success()
    {
        $response = $this->noteStoreResponse();

        $response->assertStatus(201);
    }

    public function test_should_response_with_latest_note_when_validation_success()
    {
        $response = $this->noteStoreResponse();

        $response->assertJsonPath(
            'data.title',
            'AnjayMabar'
        );
    }

    public function test_should_response_with_note_when_validation_success()
    {
        $response = $this->noteStoreResponse();

        $response->assertJsonStructure([
            'data' => [
                'title',
                'content',
                'created_at'
            ]
        ]);
    }
}
