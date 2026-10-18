<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class PasswordForm extends Form
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'max:50', 'confirmed', 'different:current_password'],
        ];
    }

    /**
     * Saves the new password and clears the forced password change flag.
     */
    public function update(User $user): void
    {
        $this->validate();

        $user->update([
            'password' => $this->password,
            'change_password' => false,
        ]);

        activity('auth')->performedOn($user)->causedBy($user)->event('password_changed')->log('Password changed');

        $this->reset();
    }
}
