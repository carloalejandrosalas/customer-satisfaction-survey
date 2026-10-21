<?php

namespace App\Livewire\Surveys;

use App\Models\Survey;
use Flux\Flux;
use Livewire\Component;

class Show extends Component
{
    public Survey $survey;

    public function mount(int $id): void
    {
        $this->survey = Survey::with(['creator', 'category', 'questions'])
            ->withCount(['questions', 'customers', 'answers'])
            ->findOrFail($id);
    }

    public function showConfirmDelete(): void
    {
        Flux::modal('confirm-delete')->show();
    }

    public function deleteSurvey(): void
    {
        $this->survey->delete();

        Flux::toast(variant: 'success', text: 'Survey successfully deleted.');

        $this->redirectRoute('surveys.index', navigate: true);
    }

    public function render()
    {
        /** @disregard P1013 title() is a Livewire view macro */
        return view('livewire.surveys.show')->title("Edit: {$this->survey->name}");
    }
}
