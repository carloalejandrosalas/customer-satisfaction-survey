<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\RoleEnum;
use Illuminate\Validation\Rule;
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
            'address' => ['required', 'string', 'max:50'],
            'role' => ['required', Rule::enum(RoleEnum::class)],
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
                'password' => ['required', 'string', 'password', 'max:50'],
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

    public function save()
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

        // If creating a new user, include the password in the fields to be saved.
        if (! isset($this->user) || ! $this->user) {
            $commonFields[] = 'password';
        }

        // Retrieve only the fields that are present in the $commonFields array.
        $userData = $this->only($commonFields);

        // Depending on whether the user exists, update the existing user or create a new one.
        isset($this->user) && $this->user ? $this->user->update($userData) : User::create($userData);

        $this->reset();
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
        $this->user = null;
    }
}
