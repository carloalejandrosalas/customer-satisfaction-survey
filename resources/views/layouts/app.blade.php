<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @auth
        <script>
            // Apply the signed-in user's saved theme and, on first visit to this device, the sidebar default.
            (() => {
                const theme = @js(auth()->user()->setting('theme', 'system'));
                if (theme === 'system') {
                    localStorage.removeItem('flux.appearance');
                } else {
                    localStorage.setItem('flux.appearance', theme);
                }

                if (localStorage.getItem('flux-sidebar-collapsed-desktop') === null) {
                    localStorage.setItem('flux-sidebar-collapsed-desktop',
                        JSON.stringify(@js(auth()->user()->setting('sidebar', 'expanded')) === 'collapsed'));
                }
            })();
        </script>
    @endauth
    @fluxAppearance
</head>

<body class="min-h-dvh bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    @auth
        <flux:sidebar sticky collapsible class="border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.header class="mb-2">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex min-w-0 flex-1 items-center gap-3 rounded-xl p-1 in-data-flux-sidebar-collapsed-desktop:hidden">
                    <span
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-md shadow-indigo-500/30">
                        <flux:icon name="chat-bubble-left-right" class="size-5" />
                    </span>
                    <span class="min-w-0 leading-tight">
                        <span
                            class="block truncate text-base font-semibold tracking-tight text-zinc-900 dark:text-white">{{ config('app.name', 'Laravel') }}</span>
                        <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">Customer Surveys</span>
                    </span>
                </a>

                <flux:sidebar.collapse
                    class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="gap-1">
                <p
                    class="px-3 pb-1 pt-2 text-xs font-semibold uppercase tracking-wider text-zinc-400 in-data-flux-sidebar-collapsed-desktop:hidden dark:text-zinc-500">
                    Menu
                </p>
                @foreach (config('navigation.sidebar_links') as $link)
                    <flux:sidebar.item :icon="$link['icon']" :href="route($link['route'])" wire:navigate
                        :current="request()->routeIs($link['active'] ?? $link['route'])"
                        class="h-11! gap-3.5 **:data-content:text-base **:data-flux-icon:size-5 data-current:bg-indigo-50! data-current:text-indigo-700! data-current:font-semibold dark:data-current:bg-indigo-500/15! dark:data-current:text-indigo-300!">
                        {{ $link['label'] }}
                    </flux:sidebar.item>
                @endforeach
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile :name="auth()->user()->name" :initials="str(auth()->user()->name)->initials()" />

                <flux:menu>
                    <flux:menu.item icon="user" :href="route('profile')" wire:navigate>Profile</flux:menu.item>
                    <flux:menu.item icon="cog-6-tooth" :href="route('settings')" wire:navigate>Settings</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full text-left text-rose-600 dark:text-rose-400">
                            Log Out
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <flux:header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:brand :name="config('app.name', 'Laravel')" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="str(auth()->user()->name)->initials()" />

                <flux:menu>
                    <flux:menu.item icon="user" :href="route('profile')" wire:navigate>Profile</flux:menu.item>
                    <flux:menu.item icon="cog-6-tooth" :href="route('settings')" wire:navigate>Settings</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full text-left text-rose-600 dark:text-rose-400">
                            Log Out
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>
    @endauth

    <flux:main container class="py-6 sm:py-8">
        {{ $slot }}
    </flux:main>

    @persist('toast')
        <flux:toast />
    @endpersist


    @fluxScripts
</body>

</html>
