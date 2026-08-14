<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ $form->name !== '' ? $form->name : __('Serviço') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Dados do catálogo. O preço no orçamento poderá divergir na próxima fase.') }}</flux:text>
    </div>

    <form wire:submit="save" class="max-w-lg space-y-6">
        @include('livewire.services.partials.form')

        <div class="flex items-center gap-4">
            @if ($this->canUpdate)
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            @endif

            <flux:button :href="route('services.index')" wire:navigate>{{ __('Cancel') }}</flux:button>

            @if ($this->canDelete)
                <flux:button
                    variant="danger"
                    type="button"
                    wire:click="delete"
                    wire:confirm="{{ __('Excluir este serviço?') }}"
                >
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </form>
</div>
