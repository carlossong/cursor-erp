<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Clientes')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Customer::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Customer>
     */
    #[Computed]
    public function customers(): LengthAwarePaginator
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return Customer::query()
            ->where('company_id', $user->company_id)
            ->when($this->search !== '', function ($query) {
                $query->whereAny(
                    ['name', 'tax_id', 'email', 'phone'],
                    'like',
                    '%'.$this->search.'%',
                );
            })
            ->orderBy('name')
            ->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.customers.index');
    }
}
