<?php

use App\Models\Member;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, get, post, put, delete};
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->admin = Member::factory()->create();
    $this->actingAs($this->admin);
});

it('displays all quizzes for admin', function () {
    Quiz::factory()->count(3)->create();

    $response = get(route('quizzes.index'));

    $response->assertStatus(200)->assertViewIs('admin.quizzes.index')->assertViewHas('quizzes');
});

it('creates a quiz successfully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post('/quizzes', [
        'title' => 'New Quiz',
        'description' => 'Test quiz description',
        'duration' => 10,
    ]);

    $response->assertStatus(302); 
    $this->assertDatabaseHas('quizzes', [
        'title' => 'New Quiz',
        'created_by' => $user->id,
    ]);
});

it('updates a quiz successfully', function () {
    $quiz = Quiz::factory()->create();

    $data = [
        'title' => 'Updated Quiz',
        'description' => 'Updated description',
        'duration' => 15,
    ];

    $response = put(route('quizzes.update', $quiz), $data);

    $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Quiz updated successfully!',
             ]);

    expect(Quiz::where('title', 'Updated Quiz')->exists())->toBeTrue();
});

it('deletes a quiz successfully', function () {
    $quiz = Quiz::factory()->create();

    $response = delete(route('quizzes.destroy', $quiz));

    $response->assertRedirect(route('admin.dashboard'));
    expect(Quiz::find($quiz->id))->toBeNull();
});

afterEach(function () {
    Schema::enableForeignKeyConstraints();
});
