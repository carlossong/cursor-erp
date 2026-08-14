<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <flux:heading size="xl" level="1">{{ __('Serviços') }}</flux:heading>

        <div class="flex items-center gap-2">
            @can('viewAny', App\Models\ServiceCategory::class)
                <flux:button :href="route('service-categories.index')" wire:navigate>
                    {{ __('Categorias') }}
                </flux:button>
            @endcan

            @can('create', App\Models\Service::class)
                <flux:button variant="primary" :href="route('services.create')" wire:navigate>
                    {{ __('Novo serviço') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <flux:input
        wire:model.live.debounce.300ms="search"
        :label="__('Buscar')"
        :placeholder="__('Código, nome ou descrição')"
        icon="magnifying-glass"
    />

    <flux:table :paginate="$this->services">
        <flux:table.columns>
            <flux:table.column>{{ __('Código') }}</flux:table.column>
            <flux:table.column>{{ __('Nome') }}</flux:table.column>
            <flux:table.column>{{ __('Categoria') }}</flux:table.column>
            <flux:table.column>{{ __('Unidade') }}</flux:table.column>
            <flux:table.column>{{ __('Preço') }}</flux:table.column>
            @if ($this->canViewCost)
                <flux:table.column>{{ __('Custo') }}</flux:table.column>
            @endif
            <flux:table.column>{{ __('Faturamento') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->services as $service)
                <flux:table.row :key="$service->id">
                    <flux:table.cell>{{ $service->code }}</flux:table.cell>
                    <flux:table.cell variant="strong">
                        <flux:link :href="route('services.edit', $service)" wire:navigate>
                            {{ $service->name }}
                        </flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $service->category?->name }}</flux:table.cell>
                    <flux:table.cell>{{ $service->unit->label() }}</flux:table.cell>
                    <flux:table.cell>R$ {{ number_format((float) $service->default_price, 2, ',', '.') }}</flux:table.cell>
                    @if ($this->canViewCost)
                        <flux:table.cell>R$ {{ number_format((float) $service->default_cost, 2, ',', '.') }}</flux:table.cell>
                    @endif
                    <flux:table.cell>{{ $service->billing_mode->label() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$service->is_active ? 'green' : 'zinc'">
                            {{ $service->is_active ? __('Ativo') : __('Inativo') }}
                        </flux:badge>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
