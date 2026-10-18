<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Url;
use Livewire\Component;

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

    public bool $isFormOpen = false;

    public UserForm $form;

    public function editUser(User $selectedUser)
    {
        $this->selectedUser = $selectedUser;
        $this->form->setUser($selectedUser);
        $this->isFormOpen = true;
    }

    public function showConfirmDelete(User $user)
    {
        $this->selectedUser = $user;
        Flux::modal('confirm-delete')->show();
    }

    public function deleteUser(): void
    {
        if ($this->selectedUser && $this->selectedUser->isNot(auth()->user())) {
            $this->selectedUser->delete();
        }

        $this->selectedUser = null;
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
