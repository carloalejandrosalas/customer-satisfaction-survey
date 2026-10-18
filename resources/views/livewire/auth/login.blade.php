<div>
    <div class="flex min-h-dvh items-center justify-center px-4">
        <flux:card class="w-full max-w-96" body="inset" variant="soft" size="lg">
            <flux:card.header>
                <h3 class="text-center text-xl">Telcom Inc. 🌎</h3>
            </flux:card.header>
            <flux:card.body>
                <form wire:submit="login">
                    <flux:fieldset>
                        <flux:field>
                            <flux:label>E-mail</flux:label>
                            <flux:input type="text" wire:model="email" />
                            <flux:error name="email" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Password</flux:label>
                            <flux:input type="password" value="password" viewable wire:model="password" />
                            <flux:error name="password" />
                        </flux:field>
                    </flux:fieldset>

                    <flux:button type="submit" class="mt-4 w-full">
                        Login
                    </flux:button>
                </form>
            </flux:card.body>
        </flux:card>
    </div>
</div>
