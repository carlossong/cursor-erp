<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <flux:heading size="xl" level="1">{{ __('Clientes') }}</flux:heading>

        @can('create', App\Models\Customer::class)
            <flux:button variant="primary" :href="route('customers.create')" wire:navigate>
                {{ __('Novo cliente') }}
            </flux:button>
        @endcan
    </div>

    <flux:input
        wire:model.live.debounce.300ms="search"
        :label="__('Buscar')"
        :placeholder="__('Nome, documento, e-mail ou telefone')"
        icon="magnifying-glass"
    />

    <flux:table :paginate="$this->customers">
        <flux:table.columns>
            <flux:table.column>{{ __('Nome') }}</flux:table.column>
            <flux:table.column>{{ __('Tipo') }}</flux:table.column>
            <flux:table.column>{{ __('Documento') }}</flux:table.column>
            <flux:table.column>{{ __('E-mail') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->customers as $customer)
                <flux:table.row :key="$customer->id">
                    <flux:table.cell variant="strong">
                        <flux:link :href="route('customers.edit', $customer)" wire:navigate>
                            {{ $customer->name }}
                        </flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $customer->person_type->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $customer->tax_id }}</flux:table.cell>
                    <flux:table.cell>{{ $customer->email }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$customer->is_active ? 'green' : 'zinc'">
                            {{ $customer->is_active ? __('Ativo') : __('Inativo') }}
                        </flux:badge>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
