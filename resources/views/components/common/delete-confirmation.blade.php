@props([
    'name' => 'confirm-delete',
    'title' => 'Delete item?',
    'text' => 'This action cannot be reversed.',
    'confirmLabel' => 'Delete',
    'cancelLabel' => 'Cancel',
    'action' => 'delete',
])

<flux:modal :name="$name" {{ $attributes->class('min-w-[22rem]') }}>
    <div class="space-y-6">
        <div class="space-y-2 gap-4">
            <flux:heading size="lg">{{ $title }}</flux:heading>
            <flux:text class="mt-2">{{ $text }}</flux:text>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">{{ $cancelLabel }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="{{ $action }}" wire:loading.attr="disabled"
                wire:target="{{ $action }}">
                {{ $confirmLabel }}
            </flux:button>
        </div>
</flux:modal>
