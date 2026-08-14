<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <flux:heading size="xl" level="1">{{ __('Usuários') }}</flux:heading>

        @can('create', App\Models\User::class)
            <flux:button variant="primary" :href="route('users.create')" wire:navigate>
                {{ __('Novo usuário') }}
            </flux:button>
        @endcan
    </div>

    <flux:table :paginate="$this->users">
        <flux:table.columns>
            <flux:table.column>{{ __('Nome') }}</flux:table.column>
            <flux:table.column>{{ __('E-mail') }}</flux:table.column>
            <flux:table.column>{{ __('Papel') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>{{ $user->roles->pluck('name')->join(', ') }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
