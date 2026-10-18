@props([
    'title',
    'eyebrow' => null,
    'subtitle' => null,
])

<header
    {{ $attributes->class('flex flex-col gap-5 border-b border-zinc-200 pb-6 dark:border-zinc-800 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="space-y-1">
        @if ($eyebrow)
            <flux:text class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400">
                {{ $eyebrow }}
            </flux:text>
        @endif
        <flux:heading size="xl" level="1" class="tracking-tight">{{ $title }}</flux:heading>
        @if ($subtitle)
            <flux:subheading>{{ $subtitle }}</flux:subheading>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
            {{ $slot }}
        </div>
    @endif
</header>
