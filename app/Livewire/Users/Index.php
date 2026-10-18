<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('User Management')]
class Index extends Component
{
    // GET parameters for sorting, pagination, and search.
    #[Url(except: 'created_at')]
    public $sortBy = 'created_at';

    #[Url(except: 'desc')]
    public $sortDirection = 'desc';

    #[Url(except: 1)]
    public $page = 1;

    #[Url(except: '')]
    public $search = '';

    // Selection state for the currently selected user.
    public ?User $selectedUser = null;

    public UserForm $form;

    public function editUser(User $selectedUser)
    {
        $this->selectedUser = $selectedUser;
        $this->form->setUser($selectedUser);
        Flux::modal('user-form')->show();
    }

    /**
     * Save the user form data.
     * If a user is selected, it updates the user.
     * Otherwise, it creates a new user.
     */
    public function save()
    {
        $message = '';

        // Apply corresponding action based on whether a user is selected.
        if ($this->selectedUser) {
            $this->form->update();
            $message = 'User successfully updated.';
        } else {
            $this->form->store();

            $message = 'User successfully created.';
        }

        // Show the toast message, reset the selection and close the modal.
        $this->selectedUser = null;
        Flux::toast(variant: 'success', text: $message);
        Flux::modal('user-form')->close();
    }

    /**
     * Open the form modal empty, to create a new user.
     */
    public function addUser()
    {
        $this->selectedUser = null;
        $this->form->resetForm();
        $this->form->resetValidation();
        Flux::modal('user-form')->show();
    }

    /**
     * Show the confirmation modal for deleting a user.
     */
    public function showConfirmDelete(User $user)
    {
        $this->selectedUser = $user;
        Flux::modal('confirm-delete')->show();
    }

    /**
     * Delete the selected user if it is not the currently authenticated user.
     */
    public function deleteUser(): void
    {
        if ($this->selectedUser && $this->selectedUser->isNot(auth()->user())) {
            $this->selectedUser->delete();
        }

        $this->selectedUser = null;
        Flux::toast(variant: 'info', text: 'User successfully deleted.');
        Flux::modal('confirm-delete')->close();
    }

    public function updatedSearch()
    {
        $this->page = 1;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function sort(string $column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function gotoPage(int $page)
    {
        $this->page = $page;
    }

    public function render()
    {
        $users = User::where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orWhere('phone_number', 'like', "%{$this->search}%")
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10, ['*'], 'page', $this->page);

        return view('livewire.users.index', [
            'users' => $users,
        ]);
    }
}
