<x-page class="max-w-3xl">
    <x-page-header eyebrow="Account" title="Settings" subtitle="Personalize how the platform looks for you." />

    <form wire:submit="save" class="space-y-6" x-data
        x-on:settings-saved.window="
            $flux.appearance = $event.detail.theme;
            localStorage.setItem('flux-sidebar-collapsed-desktop', JSON.stringify($event.detail.sidebar === 'collapsed'));
        ">
        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">Appearance</flux:heading>
                <flux:subheading>Choose the theme and the default sidebar layout.</flux:subheading>
            </div>

            <flux:radio.group wire:model="form.theme" label="Theme" variant="segmented">
                <flux:radio value="light" icon="sun" label="Light" />
                <flux:radio value="dark" icon="moon" label="Dark" />
                <flux:radio value="system" icon="computer-desktop" label="System" />
            </flux:radio.group>

            <flux:radio.group wire:model="form.sidebar" label="Sidebar" variant="segmented"
                description="Applies the first time you open the platform on a device; you can still toggle it any time.">
                <flux:radio value="expanded" label="Expanded" />
                <flux:radio value="collapsed" label="Collapsed" />
            </flux:radio.group>
        </flux:card>

        <div class="flex justify-end mt-6">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                Save settings
            </flux:button>
        </div>
    </form>
</x-page>
