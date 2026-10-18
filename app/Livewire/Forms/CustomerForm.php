<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CustomerForm extends Form
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email_address = '';

    public string $phone_number = '';

    public string $state = '';

    public string $city = '';

    public string $residence_address = '';

    public bool $status = true;

    public ?Customer $customer = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $ignoredId = $this->customer?->id;

        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email_address' => [
                'required', 'string', 'email', 'max:100',
                Rule::unique('customers', 'email_address')->ignore($ignoredId)->whereNull('deleted_at'),
            ],
            'phone_number' => [
                'required', 'string', 'max:50',
                Rule::unique('customers', 'phone_number')->ignore($ignoredId)->whereNull('deleted_at'),
            ],
            'state' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:50'],
            'residence_address' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'first_name', 'last_name', 'email_address', 'phone_number',
            'state', 'city', 'residence_address', 'status',
        ];
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;

        foreach ($this->fields() as $field) {
            $this->{$field} = $customer->{$field};
        }
    }

    public function store(): void
    {
        $this->validate();

        Customer::create($this->only($this->fields()));

        $this->resetForm();
    }

    public function update(): void
    {
        $this->validate();

        $this->customer?->update($this->only($this->fields()));

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->resetValidation();
    }
}
