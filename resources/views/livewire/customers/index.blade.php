<x-page>
    <x-page-header eyebrow="Customers" title="Customer Management" subtitle="Manage the customers you survey.">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
            placeholder="Name, Email, or Phone #" />
        <flux:button color="blue" variant="outline" icon="plus" wire:click="addCustomer">New</flux:button>
    </x-page-header>

    <flux:table :paginate="$customers">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'first_name'" :direction="$sortDirection"
                wire:click="sort('first_name')">First Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'last_name'" :direction="$sortDirection"
                wire:click="sort('last_name')">Last Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'email_address'" :direction="$sortDirection"
                wire:click="sort('email_address')">Email</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'phone_number'" :direction="$sortDirection"
                wire:click="sort('phone_number')">Phone #</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($customers as $customer)
                <flux:table.row :key="$customer->id">
                    <flux:table.cell>{{ $customer->first_name }}</flux:table.cell>
                    <flux:table.cell>{{ $customer->last_name }}</flux:table.cell>
                    <flux:table.cell>{{ $customer->email_address }}</flux:table.cell>
                    <flux:table.cell>{{ $customer->phone_number }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$customer->status ? 'green' : 'zinc'">
                            {{ $customer->status ? 'Active' : 'Inactive' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        <flux:button icon="pencil-square" size="sm" wire:click="editCustomer({{ $customer->id }})" />
                        <flux:button icon="trash" size="sm" color="red"
                            wire:click="showConfirmDelete({{ $customer->id }})" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center">No customers found.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="customer-form" class="w-full max-w-md space-y-4">
        <flux:heading size="lg">{{ $selectedCustomer ? 'Edit Customer' : 'New Customer' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="First Name" wire:model="form.first_name" required />
                <flux:input label="Last Name" wire:model="form.last_name" required />
            </div>
            <flux:input type="email" label="Email" wire:model="form.email_address" required />
            <flux:input label="Phone #" wire:model="form.phone_number" required />
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="State" wire:model="form.state" required />
                <flux:input label="City" wire:model="form.city" required />
            </div>
            <flux:input label="Residence Address" wire:model="form.residence_address" required />
            <flux:switch label="Active" wire:model="form.status" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button wire:click="form.resetForm" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    <x-common.delete-confirmation name="confirm-delete" action="deleteCustomer" title="Delete customer?"
        :text="'You\'re about to delete ' . ($selectedCustomer?->name ?? 'this customer') . '. This action cannot be reversed.'"
        confirm-label="Delete customer" />
</x-page>
