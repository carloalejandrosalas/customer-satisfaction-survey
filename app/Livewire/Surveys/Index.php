<?php

namespace App\Livewire\Surveys;

use App\Livewire\Concerns\WithSearchAndSort;
use App\Livewire\Forms\SurveyForm as FormsSurveyForm;
use App\Models\Survey;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Surveys')]
class Index extends Component
{
    use WithSearchAndSort;

    public ?Survey $selectedSurvey = null;

    public FormsSurveyForm $form;

    protected function sortableColumns(): array
    {
        return ['name', 'description', 'category.name', 'creator.name', 'created_at'];
    }

    /**
     * Creates a new survey.
     */
    public function addSurvey(): void
    {
        $this->selectedSurvey = null;
        $this->form->resetForm();
        Flux::modal('survey-form')->show();
    }

    /**
     * Proceed with editing the given survey.
     */
    public function editSurvey(Survey $survey): void
    {
        $this->selectedSurvey = $survey;
        $this->form->resetValidation();
        $this->form->setSurvey($survey);
        Flux::modal('survey-form')->show();
    }

    /**
     * Show the confirmation modal for deleting the given survey.
     */
    public function showConfirmDelete(Survey $survey): void
    {
        $this->selectedSurvey = $survey;
        Flux::modal('confirm-delete')->show();
    }

    /**
     * Navigates to the survey details page for the given survey ID.
     */
    public function navigateToSurvey(int $surveyId): void
    {
        redirect()->route('surveys.show', ['id' => $surveyId]);
    }

    /**
     * Creates the survey, or updates it when one is selected.
     */
    public function save(): void
    {
        if ($this->selectedSurvey) {
            $this->form->update();
            $message = 'Survey successfully updated.';
        } else {
            $this->form->store();
            $message = 'Survey successfully created.';
        }

        $this->selectedSurvey = null;
        Flux::toast(variant: 'success', text: $message);
        Flux::modal('survey-form')->close();
    }

    public function render()
    {
        return view('livewire.surveys.index', [
            'surveys' => Survey::query()
                ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhereHas('category', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('creator', fn ($query) => $query->where('name', 'like', "%{$this->search}%")))
                ->orderBy($this->sortBy ?? 'name', $this->sortDirection ?? 'asc')
                ->paginate(10),
        ]);
    }
}
