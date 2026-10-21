@php
    $typeStyles = [
        'YES_NO' => ['label' => 'Yes / No', 'color' => 'lime', 'icon' => 'check-circle'],
        'TEXT' => ['label' => 'Text', 'color' => 'blue', 'icon' => 'chat-bubble-left-ellipsis'],
        'POINTS' => ['label' => 'Points', 'color' => 'yellow', 'icon' => 'star'],
    ];
@endphp

<x-page>
    <div class="space-y-6">
        <flux:button :href="route('surveys.index')" wire:navigate variant="subtle" size="sm" icon="arrow-left"
            class="-ml-2">
            All surveys
        </flux:button>

        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($survey->category)
                        <flux:badge color="indigo" size="sm" inset="top bottom">{{ $survey->category->name }}
                        </flux:badge>
                    @endif
                    <flux:text class="text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">
                        Survey #{{ $survey->id }}
                    </flux:text>
                </div>
                <flux:heading size="xl" level="1" class="tracking-tight">{{ $survey->name }}</flux:heading>
                <flux:text class="max-w-2xl">{{ $survey->description ?: 'No description provided.' }}</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:button variant="danger" icon="trash" wire:click="showConfirmDelete">Delete</flux:button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([['Questions', $survey->questions_count, 'list-bullet'], ['Customers', $survey->customers_count, 'users'], ['Answers', $survey->answers_count, 'chat-bubble-bottom-center-text']] as [$label, $value, $icon])
            <div
                class="flex items-center gap-4 rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div
                    class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <flux:icon :name="$icon" class="size-5" />
                </div>
                <div>
                    <div class="text-2xl font-semibold tracking-tight">{{ number_format($value) }}</div>
                    <flux:text class="text-sm">{{ $label }}</flux:text>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section
            class="rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-2">
            <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                <flux:heading size="lg">Questions</flux:heading>
                <flux:text class="text-sm">{{ $survey->questions_count }} total</flux:text>
            </div>

            @forelse ($survey->questions as $question)
                @php($style = $typeStyles[$question->type] ?? ['label' => 'Invalid', 'color' => 'red', 'icon' => 'exclamation-triangle'])
                <div wire:key="question-{{ $question->id }}"
                    class="flex items-center gap-4 border-b border-zinc-100 px-6 py-4 last:border-b-0 dark:border-zinc-800/60">
                    <span
                        class="flex size-7 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ $loop->iteration }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium">{{ $question->text }}</p>
                    </div>
                    <flux:badge :color="$style['color']" :icon="$style['icon']" size="sm">{{ $style['label'] }}
                    </flux:badge>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <flux:text>This survey has no questions yet.</flux:text>
                </div>
            @endforelse
        </section>

        <aside class="h-fit rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                <flux:heading size="lg">Details</flux:heading>
            </div>
            <dl class="space-y-5 px-6 py-5 text-sm">
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                        {{ str($survey->creator?->name ?? '?')->substr(0, 1)->upper() }}
                    </span>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-zinc-500">Creator</dt>
                        <dd class="font-medium">{{ $survey->creator?->name ?? 'N/A' }}</dd>
                    </div>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-zinc-500">Category</dt>
                    <dd class="font-medium">{{ $survey->category?->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-zinc-500">Created</dt>
                    <dd class="font-medium">{{ $survey->created_at->diffForHumans() }}</dd>
                    <dd class="text-zinc-500">{{ $survey->created_at->isoFormat('LLL') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-zinc-500">Last updated</dt>
                    <dd class="font-medium">{{ $survey->updated_at->diffForHumans() }}</dd>
                    <dd class="text-zinc-500">{{ $survey->updated_at->isoFormat('LLL') }}</dd>
                </div>
            </dl>
        </aside>
    </div>

    <x-common.delete-confirmation name="confirm-delete" action="deleteSurvey" title="Delete survey?" :text="'You\'re about to delete ' . $survey->name . '. This action cannot be reversed.'"
        confirm-label="Delete survey" />
</x-page>
