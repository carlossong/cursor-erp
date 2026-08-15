<?php

namespace App\Livewire\Customers;

use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo cliente')]
class Create extends Component
{
    public CustomerForm $form;

    public function mount(): void
    {
        $this->authorize('create', Customer::class);
        $this->form->contacts = [CustomerForm::emptyContact(primary: true)];
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
        $this->authorize('create', Customer::class);

        $actor = Auth::user();

        abort_unless($actor instanceof User, 403);

        $this->form->store($actor);

        Flux::toast(variant: 'success', text: __('Cliente criado.'));

        $this->redirect(route('customers.index'), navigate: true);
    }
}
