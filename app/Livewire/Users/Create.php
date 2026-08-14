<?php

namespace App\Livewire\Users;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo usuário')]
class Create extends Component
{
    use PasswordValidationRules, ProfileValidationRules;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'comercial';

    public bool $is_active = true;

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    public function save(): void
    {
        $this->authorize('create', User::class);

        $actor = Auth::user();

        abort_unless($actor instanceof User, 403);

        $validated = $this->validate([
            ...$this->profileRules(),
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'role' => ['required', 'string', Rule::in(['admin', 'comercial', 'operacao', 'financeiro', 'gestor'])],
            'is_active' => ['boolean'],
        ]);

        $user = new User;
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'password' => $validated['password'],
            'is_active' => $validated['is_active'],
        ]);
        $user->company_id = $actor->company_id;
        $user->email_verified_at = now();
        $user->save();
        $user->assignRole($validated['role']);

        Flux::toast(variant: 'success', text: __('Usuário criado.'));

        $this->redirect(route('users.index'), navigate: true);
    }
}
