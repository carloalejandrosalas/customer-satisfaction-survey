<?php

namespace App\Livewire\Settings;

use App\Livewire\Forms\SettingsForm;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Settings')]
class Show extends Component
{
    public SettingsForm $form;

    public function mount(): void
    {
        $this->form->setUser(Auth::user());
    }

    public function save(): void
    {
        $this->form->update(Auth::user());

        $this->dispatch('settings-saved', theme: $this->form->theme, sidebar: $this->form->sidebar);

        Flux::toast(variant: 'success', text: 'Settings successfully updated.');
    }

    public function render()
    {
        return view('livewire.settings.show');
    }
}
