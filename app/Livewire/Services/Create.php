<?php

namespace App\Livewire\Services;

use App\Livewire\Forms\ServiceForm;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo serviço')]
class Create extends Component
{
    public ServiceForm $form;

    public function mount(): void
    {
        $this->authorize('create', Service::class);
    }

    public function save(): void
    {
        $this->authorize('create', Service::class);

        $actor = Auth::user();

        abort_unless($actor instanceof User, 403);

        $this->form->store($actor);

        Flux::toast(variant: 'success', text: __('Serviço criado.'));

        $this->redirect(route('services.index'), navigate: true);
    }

    /**
     * @return Collection<int, ServiceCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return ServiceCategory::query()
            ->where('company_id', $user->company_id)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function canViewCost(): bool
    {
        return Auth::user()?->can('services.view-cost') ?? false;
    }
}
