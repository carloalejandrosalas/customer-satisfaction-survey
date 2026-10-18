<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title . ' | ' . config('app.name') ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="min-h-dvh bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    @auth
        <flux:sidebar sticky collapsible class="border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.header>
                <flux:sidebar.brand href="{{ route('dashboard') }}" :name="config('app.name', 'Laravel')" />
                <flux:sidebar.collapse
                    class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                @foreach (config('navigation.sidebar_links') as $link)
                    <flux:sidebar.item :icon="$link['icon']" :href="route($link['route'])"
                        :current="request()->routeIs($link['active'] ?? $link['route'])">
                        {{ $link['label'] }}
                    </flux:sidebar.item>
                @endforeach
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile :name="auth()->user()->name" :initials="str(auth()->user()->name)->initials()" />

                <flux:menu>
                    <flux:menu.item icon="user" wire:click="openProfileModal">Profile</flux:menu.item>
                    <flux:menu.item icon="cog-6-tooth" wire:click="openSettingsModal">Settings</flux:menu.item>
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
                    <flux:menu.item icon="user" wire:click="openProfileModal">Profile</flux:menu.item>
                    <flux:menu.item icon="cog-6-tooth" wire:click="openSettingsModal">Settings</flux:menu.item>
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
