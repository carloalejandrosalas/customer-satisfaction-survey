<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\RoleEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class UserForm extends Form
{
    // User's personal information.
    public string $name = '';

    public string $email = '';

    public string $password = ''; // Password is only required when creating a new user

    public string $state = '';

    public string $city = '';

    public string $address = '';

    public string $phone_number = '';

    public string $role = '';

    public ?User $user = null;

    public array $availableRoles = [];

    protected function rules(): array
    {
        $commonFields = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:50'],
            'state' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(array_column(RoleEnum::cases(), 'name'))],
            'phone_number' => ['required', 'string', 'max:50'],
        ];

        // If updating an existing user, password is not required.
        if ($this->user) {
            return array_merge($commonFields, [
                'email' => ['required', 'string', 'email', 'max:50', Rule::unique('users')->ignore($this->user->id)],
            ]);
        }

        // If creating a new user, password is required.
        return array_merge(
            $commonFields,
            [
                'email' => ['required', 'string', 'email', 'max:50', Rule::unique('users')],
                'password' => ['required', 'string', Password::defaults(), 'max:50'],
            ]
        );
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        $this->name = $user->name;
        $this->email = $user->email;
        $this->state = $user->state;
        $this->city = $user->city;
        $this->address = $user->address;
        $this->phone_number = $user->phone_number;
        $this->role = $user->role;
    }

    public function roleOptions()
    {
        return collect(RoleEnum::cases())
            ->mapWithKeys(fn ($s) => [$s->name => $s->name])
            ->all();
    }

    public function store()
    {
        $this->validate();

        $commonFields = [
            'name',
            'email',
            'state',
            'city',
            'address',
            'phone_number',
            'role',
        ];

        $userData = [];

        // Retrieve only the fields that are present in the $commonFields array.
        $userData = $this->only($commonFields);

        User::create([
            ...$userData,
            'password' => Hash::make($this->password),
            'change_password' => true,
        ]);

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        if ($this->user) {
            $this->user->update($this->only([
                'name',
                'email',
                'state',
                'city',
                'address',
                'phone_number',
                'role',
            ]));
        }

        $this->reset();
    }

    /**
     * Sets a new password for the selected user and forces them to change it on their next visit.
     */
    public function changePassword(): void
    {
        $this->validateOnly('password', [
            'password' => ['required', 'string', Password::defaults(), 'max:50'],
        ]);

        $this->user?->update([
            'password' => Hash::make($this->password),
            'change_password' => true,
        ]);

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->state = '';
        $this->city = '';
        $this->address = '';
        $this->phone_number = '';
        $this->role = '';
        $this->password = '';
        $this->user = null;
    }
}
