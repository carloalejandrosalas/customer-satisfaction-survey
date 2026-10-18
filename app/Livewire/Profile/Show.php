<?php

namespace App\Livewire\Profile;

use App\Livewire\Forms\PasswordForm;
use App\Livewire\Forms\ProfileForm;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Profile')]
class Show extends Component
{
    public ProfileForm $profile;

    public PasswordForm $passwordForm;

    public function mount(): void
    {
        $this->profile->setUser(auth()->user());
    }

    public function updateProfile(): void
    {
        $this->profile->update(auth()->user());

        Flux::toast(variant: 'success', text: 'Profile successfully updated.');
    }

    public function updatePassword(): void
    {
        $wasForced = auth()->user()->change_password;

        $this->passwordForm->update(auth()->user());

        Flux::toast(variant: 'success', text: 'Password successfully updated.');

        if ($wasForced) {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.profile.show');
    }
}
