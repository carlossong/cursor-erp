<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Serviços')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Service::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Service>
     */
    #[Computed]
    public function services(): LengthAwarePaginator
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        $query = Service::query()
            ->where('company_id', $user->company_id)
            ->with('category')
            ->when($this->search !== '', function ($query) {
                $query->whereAny(
                    ['code', 'name', 'description'],
                    'like',
                    '%'.$this->search.'%',
                );
            })
            ->orderBy('name');

        if ($user->cannot('services.view-cost')) {
            $query->select([
                'id',
                'company_id',
                'category_id',
                'code',
                'name',
                'description',
                'unit',
                'default_price',
                'billing_mode',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
        }

        return $query->paginate(15);
    }

    #[Computed]
    public function canViewCost(): bool
    {
        return Auth::user()?->can('services.view-cost') ?? false;
    }

    public function render(): View
    {
        return view('livewire.services.index');
    }
}
