<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class SettingsForm extends Form
{
    public string $theme = 'system';

    public string $sidebar = 'expanded';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'theme' => ['required', Rule::in(['light', 'dark', 'system'])],
            'sidebar' => ['required', Rule::in(['expanded', 'collapsed'])],
        ];
    }

    public function setUser(User $user): void
    {
        $this->theme = $user->setting('theme', 'system');
        $this->sidebar = $user->setting('sidebar', 'expanded');
    }

    public function update(User $user): void
    {
        $this->validate();

        $user->update([
            'settings' => [...($user->settings ?? []), ...$this->all()],
        ]);
    }
}
