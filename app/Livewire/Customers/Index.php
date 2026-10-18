<?php

namespace App\Livewire\Customers;

use App\Livewire\Concerns\WithSearchAndSort;
use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Customer Management')]
class Index extends Component
{
    use WithSearchAndSort;

    public ?Customer $selectedCustomer = null;

    public CustomerForm $form;

    protected function sortableColumns(): array
    {
        return ['first_name', 'last_name', 'email_address', 'phone_number', 'status', 'created_at'];
    }

    public function addCustomer(): void
    {
        $this->selectedCustomer = null;
        $this->form->resetForm();
        Flux::modal('customer-form')->show();
    }

    public function editCustomer(Customer $customer): void
    {
        $this->selectedCustomer = $customer;
        $this->form->resetValidation();
        $this->form->setCustomer($customer);
        Flux::modal('customer-form')->show();
    }

    /**
     * Creates the customer, or updates it when one is selected.
     */
    public function save(): void
    {
        if ($this->selectedCustomer) {
            $this->form->update();
            $message = 'Customer successfully updated.';
        } else {
            $this->form->store();
            $message = 'Customer successfully created.';
        }

        $this->selectedCustomer = null;
        Flux::toast(variant: 'success', text: $message);
        Flux::modal('customer-form')->close();
    }

    public function showConfirmDelete(Customer $customer): void
    {
        $this->selectedCustomer = $customer;
        Flux::modal('confirm-delete')->show();
    }

    public function deleteCustomer(): void
    {
        if ($this->selectedCustomer) {
            $this->selectedCustomer->delete();
            Flux::toast(variant: 'success', text: 'Customer successfully deleted.');
        }

        $this->selectedCustomer = null;
        Flux::modal('confirm-delete')->close();
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search !== '', fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhere('email_address', 'like', "%{$this->search}%")
                    ->orWhere('phone_number', 'like', "%{$this->search}%")
            ))
            ->orderBy($this->resolvedSortBy(), $this->resolvedSortDirection())
            ->paginate(10, ['*'], 'page', $this->page);

        return view('livewire.customers.index', ['customers' => $customers]);
    }
}
