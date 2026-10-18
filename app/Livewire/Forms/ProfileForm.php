<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProfileForm extends Form
{
    public string $name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $state = '';

    public string $city = '';

    public string $address = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:50',
                Rule::unique('users', 'email')->ignore(auth()->id())->whereNull('deleted_at'),
            ],
            'phone_number' => ['required', 'string', 'max:50'],
            'state' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    public function setUser(User $user): void
    {
        $this->fill($user->only(['name', 'email', 'phone_number', 'state', 'city', 'address']));
    }

    public function update(User $user): void
    {
        $this->validate();

        $user->update($this->all());
    }
}
