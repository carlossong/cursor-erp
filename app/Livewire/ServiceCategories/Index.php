<?php

namespace App\Livewire\ServiceCategories;

use App\Models\ServiceCategory;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Categorias de serviço')]
class Index extends Component
{
    use WithPagination;

    public string $name = '';

    public function mount(): void
    {
        $this->authorize('viewAny', ServiceCategory::class);
    }

    public function save(): void
    {
        $this->authorize('create', ServiceCategory::class);

        $actor = Auth::user();

        abort_unless($actor instanceof User, 403);

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(ServiceCategory::class, 'name')->where('company_id', $actor->company_id),
            ],
        ]);

        $category = new ServiceCategory;
        $category->fill($validated);
        $category->company_id = $actor->company_id;
        $category->save();

        $this->reset('name');

        Flux::toast(variant: 'success', text: __('Categoria criada.'));
    }

    public function delete(int $categoryId): void
    {
        $actor = Auth::user();

        abort_unless($actor instanceof User, 403);

        $category = ServiceCategory::query()
            ->where('company_id', $actor->company_id)
            ->findOrFail($categoryId);

        $this->authorize('delete', $category);

        $category->services()->update(['category_id' => null]);
        $category->delete();

        Flux::toast(variant: 'success', text: __('Categoria excluída.'));
    }

    /**
     * @return LengthAwarePaginator<int, ServiceCategory>
     */
    #[Computed]
    public function categories(): LengthAwarePaginator
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return ServiceCategory::query()
            ->where('company_id', $user->company_id)
            ->withCount('services')
            ->orderBy('name')
            ->paginate(15);
    }

    #[Computed]
    public function canCreate(): bool
    {
        return Auth::user()?->can('create', ServiceCategory::class) ?? false;
    }

    #[Computed]
    public function canDelete(): bool
    {
        return Auth::user()?->can('services.delete') ?? false;
    }

    public function render(): View
    {
        return view('livewire.service-categories.index');
    }
}
