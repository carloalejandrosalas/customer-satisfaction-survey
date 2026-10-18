<x-page class="max-w-3xl">
    <x-page-header eyebrow="Account" title="My Profile" subtitle="Manage your personal information and password." />

    @if (session()->has('status'))
        <flux:callout variant="warning" icon="exclamation-triangle">
            {{ session('status') }}
        </flux:callout>
    @endif

    <flux:card>
        <form wire:submit="updateProfile" class="space-y-4">
            <div>
                <flux:heading size="lg">User information</flux:heading>
                <flux:subheading>Update your name, contact details and address.</flux:subheading>
            </div>

            <flux:input label="Name" wire:model="profile.name" required />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input type="email" label="Email" wire:model="profile.email" required />
                <flux:input label="Phone #" wire:model="profile.phone_number" required />
                <flux:input label="State" wire:model="profile.state" required />
                <flux:input label="City" wire:model="profile.city" required />
            </div>
            <flux:input label="Residence Address" wire:model="profile.address" required />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" wire:target="updateProfile"
                    wire:loading.attr="disabled">Save changes</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card>
        <form wire:submit="updatePassword" class="space-y-4">
            <div>
                <flux:heading size="lg">Password</flux:heading>
                <flux:subheading>Use a long, unique password to keep your account secure.</flux:subheading>
            </div>

            <flux:input type="password" label="Current password" wire:model="passwordForm.current_password"
                viewable required />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input type="password" label="New password" wire:model="passwordForm.password" viewable
                    required />
                <flux:input type="password" label="Confirm new password"
                    wire:model="passwordForm.password_confirmation" viewable required />
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" wire:target="updatePassword"
                    wire:loading.attr="disabled">Update password</flux:button>
            </div>
        </form>
    </flux:card>
</x-page>
