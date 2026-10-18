<x-page>
    <x-page-header eyebrow="Overview" title="Dashboard" subtitle="Keep track of your key metrics and recent activity.">
        <flux:select wire:model.live="timeRange" class="w-full sm:w-48">
            <flux:select.option value="24h">Last 24 hours</flux:select.option>
            <flux:select.option value="7d">Last 7 days</flux:select.option>
            <flux:select.option value="30d">Last 30 days</flux:select.option>
            <flux:select.option value="12m">Last 12 months</flux:select.option>
        </flux:select>

        <flux:button variant="primary" icon="arrow-path" wire:click="$refresh" wire:loading.attr="disabled"
            wire:target="$refresh" class="w-full sm:w-auto">
            Refresh
        </flux:button>
    </x-page-header>


    <section aria-label="Key metrics" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($quickStats as $stat)
            <flux:card wire:key="quick-stat-{{ str($stat['label'])->slug() }}"
                class="flex h-full flex-col justify-between gap-5 rounded-2xl border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                        {{ $stat['label'] }}
                    </flux:text>
                    <flux:badge size="sm" variant="success" rounded
                        :icon="$stat['trend'] === 'up' ? 'arrow-up-right' : 'arrow-down-right'">
                        {{ $stat['change'] }}
                    </flux:badge>
                </div>

                <div class="space-y-1">
                    <p class="text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">
                        {{ $stat['value'] }}
                    </p>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Compared with the previous period
                    </flux:text>
                </div>
            </flux:card>
        @endforeach
    </section>

    <section aria-label="Analytics and activity" class="grid grid-cols-1 items-stretch gap-5 xl:grid-cols-3">
        <flux:card
            class="flex h-full flex-col gap-6 rounded-2xl border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6 xl:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">Performance analytics</flux:heading>
                    <flux:subheading>Review performance trends over time.</flux:subheading>
                </div>
                <flux:dropdown>
                    <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm"
                        aria-label="Analytics options" />
                    <flux:menu>
                        <flux:menu.item icon="arrow-down-tray">Export CSV</flux:menu.item>
                        <flux:menu.item icon="printer">Print report</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>

            <div
                class="flex min-h-72 flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-zinc-50/70 px-5 py-10 text-center dark:border-zinc-700 dark:bg-zinc-950/40">
                <span
                    class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
                    <flux:icon name="chart-bar" class="size-6" />
                </span>
                <flux:heading size="sm">Analytics will appear here</flux:heading>
                <flux:text class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                    Connect a chart source to visualize your performance over the selected time range.
                </flux:text>
            </div>
        </flux:card>

        <flux:card
            class="flex h-full flex-col gap-5 rounded-2xl border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <flux:heading size="lg">Recent activity</flux:heading>
                    <flux:subheading>Latest updates from your team.</flux:subheading>
                </div>
                <flux:badge size="sm" color="zinc" rounded>{{ count($this->recentActivity) }}</flux:badge>
            </div>

            @if (count($this->recentActivity))
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($this->recentActivity as $activity)
                        <div wire:key="recent-activity-{{ $activity['id'] }}"
                            class="flex items-start gap-3 py-4 first:pt-0 last:pb-0">
                            <span @class([
                                'flex size-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold',
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' =>
                                    $activity['status'] === 'success',
                                'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' =>
                                    $activity['status'] === 'danger',
                                'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300' => !in_array(
                                    $activity['status'],
                                    ['success', 'danger'],
                                    true),
                            ])>
                                {{ str($activity['user'])->initials() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $activity['user'] }}
                                </p>
                                <p class="mt-0.5 text-sm leading-5 text-zinc-500 dark:text-zinc-400">
                                    {{ $activity['action'] }}
                                </p>
                                <p class="mt-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                                    {{ $activity['time'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div
                    class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 px-5 py-10 text-center dark:border-zinc-700">
                    <flux:icon name="inbox" class="mb-3 size-7 text-zinc-400" />
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">No recent activity yet.</flux:text>
                </div>
            @endif
        </flux:card>
    </section>

    <flux:modal wire:model.self="showProfileModal" class="min-w-[22rem]">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">Profile</flux:heading>
                <flux:subheading>Your account information</flux:subheading>
            </div>

            <div class="space-y-3">
                <div>
                    <flux:text class="text-zinc-500 dark:text-zinc-400">Name</flux:text>
                    <flux:text>{{ auth()->user()->name }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-zinc-500 dark:text-zinc-400">Email</flux:text>
                    <flux:text>{{ auth()->user()->email }}</flux:text>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="filled">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model.self="showSettingsModal" class="min-w-[22rem]">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">Settings</flux:heading>
                <flux:subheading>Choose how the dashboard appears on this device.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Appearance</flux:label>
                <flux:dropdown x-data align="start">
                    <flux:button variant="filled" icon="computer-desktop">
                        Choose appearance
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                        <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                        <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">
                            System
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:field>

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="filled">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</x-page>
