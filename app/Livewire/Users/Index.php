<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Usuários')]
class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return User::query()
            ->where('company_id', $user->company_id)
            ->active()
            ->with('roles')
            ->orderBy('name')
            ->paginate(15);
    }
}
