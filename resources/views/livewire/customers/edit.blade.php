<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ $form->name !== '' ? $form->name : __('Cliente') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Dados cadastrais, contatos e status operacional.') }}</flux:text>
    </div>

    <flux:callout variant="subtle" icon="information-circle">
        {{ __('Histórico de orçamentos, ordens de serviço e faturas estará disponível nas próximas fases.') }}
    </flux:callout>

    <form wire:submit="save" class="max-w-lg space-y-6">
        @include('livewire.customers.partials.form')

        <div class="flex items-center gap-4">
            @if ($this->canUpdate)
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            @endif

            <flux:button :href="route('customers.index')" wire:navigate>{{ __('Cancel') }}</flux:button>

            @if ($this->canDelete)
                <flux:button
                    variant="danger"
                    type="button"
                    wire:click="delete"
                    wire:confirm="{{ __('Excluir este cliente?') }}"
                >
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </form>
</div>
