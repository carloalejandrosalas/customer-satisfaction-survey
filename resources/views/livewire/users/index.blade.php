<x-page>

    <x-page-header eyebrow="Users" title="User Management" subtitle="Manage your application's users.">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
            placeholder="Name, Email, or Phone #" />
        <flux:button color="blue" variant="outline" label="Add User" icon="plus" wire:click="addUser">
            New
        </flux:button>
    </x-page-header>


    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection"
                wire:click="sort('email')">Email</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'phone_number'" :direction="$sortDirection"
                wire:click="sort('phone_number')">Phone #</flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Date</flux:table.column>

            <flux:table.column>Actions</flux:table.column>

        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="p-0">
                        {{ $user->name }}
                    </flux:table.cell>

                    <flux:table.cell class="py-0">
                        {{ $user->email }}
                    </flux:table.cell>

                    <flux:table.cell class="py-0">
                        {{ $user->phone_number ?? '000000000' }}
                    </flux:table.cell>


                    <flux:table.cell class="whitespace-nowrap">{{ $user->created_at }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        <flux:button icon="pencil-square" size="sm" wire:click="editUser({{ $user }})" />
                        @if ($user->isNot(auth()->user()))
                            <flux:button icon="trash" size="sm" color="red"
                                wire:click="showConfirmDelete({{ $user }})" />
                        @endif
                    </flux:table.cell>

                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Create / Edit Modal (single modal, reused) -->
    <flux:modal name="user-form" class="w-full max-w-md space-y-4">
        <flux:heading size="lg">
            {{ $selectedUser ? 'Edit User' : 'New User' }}
        </flux:heading>

        <form wire:submit="save">
            <flux:fieldset>
                <flux:input label="Name" wire:model="form.name" required />
                <flux:input label="Email" wire:model="form.email" required />
                @if ($selectedUser === null)
                    <flux:input type="password" label="Password" wire:model="form.password" viewable required />
                @endif
                <flux:input label="Phone #" wire:model="form.phone_number" required />
                <flux:input label="State" wire:model="form.state" required />
                <flux:input label="City" wire:model="form.city" required />
                <flux:input label="Residence Address" wire:model="form.address" required />
                <flux:select label="Role" wire:model="form.role">
                    @if ($selectedUser === null)
                        <flux:select.option value="">Select a role</flux:select.option>
                    @endif
                    @foreach ($form->roleOptions() as $roleValue => $roleLabel)
                        <flux:select.option value="{{ $roleValue }}">{{ $roleLabel }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:spacer />

                <flux:button type="submit">Save</flux:button>

                <flux:modal.close>
                    <flux:button wire:click="form.resetForm" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
            </flux:fieldset>
        </form>
    </flux:modal>

    <x-common.delete-confirmation name="confirm-delete" action="deleteUser" title="Delete user?" :text="'You\'re about to delete ' . ($selectedUser?->name ?? 'this user') . '. This action cannot be reversed.'"
        confirm-label="Delete user" />
</x-page>
