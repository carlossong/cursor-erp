<?php

namespace App\Livewire\Customers;

use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar cliente')]
class Edit extends Component
{
    public Customer $customer;

    public CustomerForm $form;

    public function mount(Customer $customer): void
    {
        $this->authorize('view', $customer);
        $this->form->fillFrom($customer);
    }

    public function addContact(): void
    {
        $this->form->addContact();
    }

    public function removeContact(int $index): void
    {
        $this->form->removeContact($index);
    }

    public function save(): void
    {
        $this->authorize('update', $this->customer);

        $this->form->update($this->customer);

        Flux::toast(variant: 'success', text: __('Cliente atualizado.'));
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->customer);

        $this->customer->delete();

        Flux::toast(variant: 'success', text: __('Cliente excluído.'));

        $this->redirect(route('customers.index'), navigate: true);
    }

    #[Computed]
    public function canUpdate(): bool
    {
        return Auth::user()?->can('update', $this->customer) ?? false;
    }

    #[Computed]
    public function canDelete(): bool
    {
        return Auth::user()?->can('delete', $this->customer) ?? false;
    }
}
