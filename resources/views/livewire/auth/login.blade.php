<div class="grid min-h-dvh lg:grid-cols-2">
    <aside
        class="relative hidden overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="pointer-events-none absolute -right-24 -top-24 size-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-16 size-96 rounded-full bg-violet-400/20 blur-3xl">
        </div>

        <div class="relative flex items-center gap-3">
            <span class="flex size-11 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/25 backdrop-blur">
                <flux:icon name="chat-bubble-left-right" class="size-6" />
            </span>
            <span class="text-xl font-semibold tracking-tight">{{ config('app.name') }}</span>
        </div>

        <div class="relative max-w-md space-y-6">
            <h2 class="text-4xl font-semibold leading-tight tracking-tight">
                Know what your customers really think.
            </h2>
            <p class="text-lg text-indigo-100">
                Create surveys, reach your customers and turn their feedback into better service.
            </p>

            <ul class="space-y-3 text-indigo-50">
                <li class="flex items-center gap-3">
                    <flux:icon name="clipboard-document-check" class="size-5 shrink-0" />
                    Build and assign customer satisfaction surveys
                </li>
                <li class="flex items-center gap-3">
                    <flux:icon name="chart-bar" class="size-5 shrink-0" />
                    Track responses and key metrics in one place
                </li>
                <li class="flex items-center gap-3">
                    <flux:icon name="users" class="size-5 shrink-0" />
                    Manage your team, products and customers
                </li>
            </ul>
        </div>

        <p class="relative text-sm text-indigo-200">&copy; {{ now()->year }} {{ config('app.name') }}</p>
    </aside>

    <main class="flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm space-y-8">
            <div class="flex items-center gap-3 lg:hidden">
                <span
                    class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-md shadow-indigo-500/30">
                    <flux:icon name="chat-bubble-left-right" class="size-5" />
                </span>
                <span class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
            </div>

            <div class="space-y-2">
                <flux:heading size="xl" level="1" class="tracking-tight">Welcome back</flux:heading>
                <flux:subheading>Sign in to your account to continue.</flux:subheading>
            </div>

            <form wire:submit="login" class="space-y-5">
                <flux:input type="email" label="Email" wire:model="email" placeholder="you@company.com"
                    icon="envelope" autocomplete="username" autofocus />

                <flux:input type="password" label="Password" wire:model="password" placeholder="Your password"
                    icon="lock-closed" viewable autocomplete="current-password" />

                <flux:checkbox wire:model="remember" label="Remember me" />

                <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled"
                    wire:target="login">
                    Sign in
                </flux:button>
            </form>
        </div>
    </main>
</div>
