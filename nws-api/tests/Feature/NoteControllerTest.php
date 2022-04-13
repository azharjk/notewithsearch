<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Note;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Sanctum::actingAs($this->user);
    }

    protected const noteIndexRouteName = 'note.index';
    protected const noteShowRouteName = 'note.show';
    protected const noteStoreRouteName = 'note.store';
    protected const noteUpdateRouteName = 'note.update';

    public function test_should_have_status_200_when_listing_a_notes()
    {
        $response = $this->get(route(self::noteIndexRouteName));

        $response->assertStatus(200);
    }

    public function test_should_response_with_list_of_notes()
    {
        $response = $this->get(route(self::noteIndexRouteName));

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'content',
                    'created_at'
                ]
            ]
        ]);
    }

    public function test_should_have_status_404_when_note_not_found()
    {
        $response = $this->get(route(self::noteShowRouteName, 1));

        $response->assertStatus(404);
    }

    public function test_should_response_with_not_found_msg_when_note_not_found()
    {
        $response = $this->get(route(self::noteShowRouteName, 1));

        $response->assertJson([
            'message' => 'Note you are looking for is not found'
        ]);
    }

    protected function noteShowFoundResponse()
    {
        $this->user->notes()->create([
            'title' => 'Meeting with HRD',
            'content' => 'maybe success'
        ]);

        return $this->get(route(self::noteShowRouteName, Note::latest()->first()->id));
    }

    public function test_should_have_status_200_when_note_found()
    {
        $response = $this->noteShowFoundResponse();

        $response->assertStatus(200);
    }

    public function test_should_response_with_note_when_note_found()
    {
        $response = $this->noteShowFoundResponse();

        $response->assertJsonPath(
            'data.title',
            'Meeting with HRD'
        );
    }

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
            'title' => 'AnjayMabar',
            'content' => 'Triple digit'
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

        $response->assertJson([
            'data' => [
                'title' => 'AnjayMabar',
                'content' => 'Triple digit'
            ]
        ]);
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

    public function test_should_have_status_404_when_note_is_not_found()
    {
        $response = $this->put(route(self::noteUpdateRouteName, 1));

        $response->assertStatus(404);
    }

    protected function createNote()
    {
        return $this->user->notes()->create([
            'title' => 'Monday',
            'content' => 'IDK'
        ]);
    }

    protected function noteUpdateSuccessResponse()
    {
        $note = $this->createNote();

        return $this->put(route(self::noteUpdateRouteName, $note->id), [
            'title' => 'Updated Monday',
            'content' => 'I Know'
        ]);
    }

    public function test_should_have_status_400_when_validation_fails()
    {
        $note = $this->createNote();

        $response = $this->put(route(self::noteUpdateRouteName, $note->id));

        $response->assertStatus(400);
    }

    public function test_should_have_status_200_when_update_successfully()
    {
        $response = $this->noteUpdateSuccessResponse();

        $response->assertStatus(200);
    }

    public function test_should_update_the_note_when_update_successfully()
    {
        $this->noteUpdateSuccessResponse();

        $this->assertDatabaseHas('notes', [
            'title' => 'Updated Monday',
            'content' => 'I Know'
        ]);

        $this->assertDatabaseMissing('notes', [
            'title' => 'Monday',
            'content' => 'I Know'
        ]);
    }
}
