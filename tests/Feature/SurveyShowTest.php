<?php

use App\Livewire\Surveys\Show;
use App\Models\Category;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('shows the survey details and its questions', function () {
    $user = User::factory()->create();
    actingAs($user);
    $survey = Survey::factory()
        ->has(SurveyQuestion::factory()->count(2), 'questions')
        ->create(['creator_id' => $user->id, 'category_id' => Category::factory()->create()->id]);

    Livewire::test(Show::class, ['id' => $survey->id])
        ->assertSee($survey->name)
        ->assertSee($survey->questions->first()->text);
});

it('deletes the survey and redirects to the index', function () {
    $user = User::factory()->create();
    actingAs($user);
    $survey = Survey::factory()->create(['creator_id' => $user->id, 'category_id' => Category::factory()->create()->id]);

    Livewire::test(Show::class, ['id' => $survey->id])
        ->call('deleteSurvey')
        ->assertRedirect(route('surveys.index'));

    expect(Survey::find($survey->id))->toBeNull();
});
