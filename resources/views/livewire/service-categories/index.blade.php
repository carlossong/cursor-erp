<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Categorias de serviço') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Agrupam o catálogo (manutenção, instalação, consultoria).') }}</flux:text>
        </div>

        <flux:button :href="route('services.index')" wire:navigate>
            {{ __('Voltar aos serviços') }}
        </flux:button>
    </div>

    @if ($this->canCreate)
        <form wire:submit="save" class="flex max-w-lg items-end gap-4">
            <div class="flex-1">
                <flux:input wire:model="name" :label="__('Nome')" type="text" required />
            </div>
            <flux:button variant="primary" type="submit">{{ __('Adicionar') }}</flux:button>
        </form>
    @endif

    <flux:table :paginate="$this->categories">
        <flux:table.columns>
            <flux:table.column>{{ __('Nome') }}</flux:table.column>
            <flux:table.column>{{ __('Serviços') }}</flux:table.column>
            @if ($this->canDelete)
                <flux:table.column></flux:table.column>
            @endif
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell variant="strong">{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $category->services_count }}</flux:table.cell>
                    @if ($this->canDelete)
                        <flux:table.cell>
                            <flux:button
                                variant="ghost"
                                size="sm"
                                wire:click="delete({{ $category->id }})"
                                wire:confirm="{{ __('Excluir esta categoria?') }}"
                            >
                                {{ __('Excluir') }}
                            </flux:button>
                        </flux:table.cell>
                    @endif
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
