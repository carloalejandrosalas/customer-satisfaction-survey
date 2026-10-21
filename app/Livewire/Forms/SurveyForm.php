<?php

namespace App\Livewire\Forms;

use App\Models\Survey;
use App\QuestionTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Form;

class SurveyForm extends Form
{
    // Form properties for survey fields
    public string $name = '';

    public string $description = '';

    public int $category = 0;

    // The creator will be filled by the authenticated user's name once the form is created.
    public string $creator = '';

    public array $questions = [];

    // The survey model instance being edited (if any)
    public ?Survey $survey = null;

    public function store()
    {
        // Validate the form data before storing.
        $this->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string|max:65535',
            'questions.*.type' => ['required', Rule::in(QuestionTypeEnum::cases())],
        ]);

        // Transaction to create survey and its questions.
        DB::transaction(function () {
            $survey = Survey::create([
                'category_id' => $this->category,
                'creator_id' => Auth::id(),
                'name' => $this->name,
                'description' => $this->description,
            ]);

            foreach ($this->questions as $questionData) {
                $survey->questions()->create([
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                ]);
            }
        });
    }

    public function update()
    {
        if (! $this->survey) {
            return; // Prevent update if no survey is set
        }

        // Prevent update if survey has answers.
        if ($this->survey->answers()->sizeof() > 0) {
            return; // Prevent update if survey has answers
        }

        $this->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string|max:65535',
            'questions.*.type' => ['required', Rule::in(QuestionTypeEnum::cases())],
        ]);

        // Transaction to update survey and its questions.
        DB::transaction(function () {
            $this->survey->update([
                'category_id' => $this->category,
                'name' => $this->name,
                'description' => $this->description,
            ]);

            // Update questions
            foreach ($this->questions as $questionData) {
                $this->survey->questions()->updateOrCreate(
                    ['id' => $questionData['id'] ?? null],
                    [
                        'text' => $questionData['text'],
                        'type' => $questionData['type'],
                    ]
                );
            }
        });

        $this->validate();
    }

    public function setSurvey(Survey $survey)
    {
        $this->survey = $survey;
        $this->category = $survey->category_id;
        $this->creator = $survey->creator->name;
        $this->name = $survey->name;
        $this->description = $survey->description;
        $this->questions = $survey->questions->toArray();
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'text' => '',
            'type' => QuestionTypeEnum::TEXT->value,
        ];
    }

    public function removeQuestion(int $index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function resetForm()
    {
        $this->survey = null;
        $this->category = '';
        $this->creator = 'null';
        $this->name = '';
        $this->description = '';
        $this->questions = [];
    }
}
