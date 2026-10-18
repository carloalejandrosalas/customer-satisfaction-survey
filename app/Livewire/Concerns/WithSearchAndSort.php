<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Url;

/**
 * Shared search, sorting and pagination state for CRUD index components.
 */
trait WithSearchAndSort
{
    #[Url(except: 'created_at')]
    public string $sortBy = 'created_at';

    #[Url(except: 'desc')]
    public string $sortDirection = 'desc';

    #[Url(except: 1)]
    public int $page = 1;

    #[Url(except: '')]
    public string $search = '';

    /**
     * Columns the index table is allowed to be sorted by.
     *
     * @return array<int, string>
     */
    abstract protected function sortableColumns(): array;

    public function updatedSearch(): void
    {
        $this->page = 1;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function sort(string $column): void
    {
        if (! in_array($column, $this->sortableColumns(), true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function gotoPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * Sort column coming from the URL, falling back to the default when it is not allowed.
     */
    protected function resolvedSortBy(): string
    {
        return in_array($this->sortBy, $this->sortableColumns(), true) ? $this->sortBy : 'created_at';
    }

    protected function resolvedSortDirection(): string
    {
        return $this->sortDirection === 'asc' ? 'asc' : 'desc';
    }
}
