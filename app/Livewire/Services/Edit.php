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

#[Title('Editar serviço')]
class Edit extends Component
{
    public Service $service;

    public ServiceForm $form;

    public function mount(Service $service): void
    {
        $this->authorize('view', $service);

        $this->service = $service;
        $canViewCost = Auth::user()?->can('viewCost', $service) ?? false;
        $this->form->fillFrom($this->service, $canViewCost);
        $this->hideCostIfUnauthorized();
    }

    public function rendering(): void
    {
        $this->hideCostIfUnauthorized();
    }

    public function save(): void
    {
        $this->authorize('update', $this->service);

        $actor = Auth::user();

        abort_unless($actor instanceof User, 403);

        $this->form->update($this->service, $actor);

        Flux::toast(variant: 'success', text: __('Serviço atualizado.'));
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->service);

        $this->service->delete();

        Flux::toast(variant: 'success', text: __('Serviço excluído.'));

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
        return Auth::user()?->can('viewCost', $this->service) ?? false;
    }

    #[Computed]
    public function canUpdate(): bool
    {
        return Auth::user()?->can('update', $this->service) ?? false;
    }

    #[Computed]
    public function canDelete(): bool
    {
        return Auth::user()?->can('delete', $this->service) ?? false;
    }

    private function hideCostIfUnauthorized(): void
    {
        if (Auth::user()?->can('viewCost', $this->service)) {
            return;
        }

        $attributes = $this->service->getAttributes();
        unset($attributes['default_cost']);
        $this->service->setRawAttributes($attributes);
        $this->service->syncOriginal();
    }
}
